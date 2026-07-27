<?php

/**
 * Usuario del prode. Independiente de Cruge: tiene su propia auth
 * con email + password (hash).
 *
 * @property integer $idProdeUsuario
 * @property string $email
 * @property string $nombre
 * @property string $passwordHash
 * @property integer $activo
 * @property integer $esAdmin
 * @property integer $idEquipo
 * @property string $createdAt
 * @property string $lastLogin
 */
class ProdeUsuario extends CActiveRecord
{
	const PUNTOS_EXACTO = 3;
	const PUNTOS_SIGNO = 1;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'prode_usuario';
	}

	public function rules()
	{
		return array(
			array('email, nombre, passwordHash', 'required'),
			array('email', 'email'),
			array('email', 'length', 'max'=>150),
			array('email', 'unique'),
			array('nombre', 'length', 'max'=>100),
			array('activo, esAdmin, idEquipo', 'numerical', 'integerOnly'=>true),
			array('idEquipo', 'default', 'setOnEmpty' => true, 'value' => null),
			array('createdAt, lastLogin', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'predicciones' => array(self::HAS_MANY, 'ProdePrediccion', 'idProdeUsuario'),
			'resetTokens' => array(self::HAS_MANY, 'ProdeResetToken', 'idProdeUsuario'),
			'equipo' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'idProdeUsuario' => 'Id',
			'email' => 'Email',
			'nombre' => 'Nombre',
			'passwordHash' => 'Password (hash)',
			'activo' => 'Activo',
			'esAdmin' => 'Es admin',
			'idEquipo' => 'Equipo',
			'createdAt' => 'Creado',
			'lastLogin' => 'Ultimo login',
		);
	}

	public function beforeSave()
	{
		if (parent::beforeSave()) {
			if ($this->isNewRecord) {
				if (empty($this->createdAt)) {
					$this->createdAt = date('Y-m-d H:i:s');
				}
			}
			return true;
		}
		return false;
	}

	public function setPassword($plain)
	{
		$this->passwordHash = password_hash($plain, PASSWORD_DEFAULT);
	}

	public function validatePassword($plain)
	{
		return password_verify($plain, $this->passwordHash);
	}

	/**
	 * Devuelve la cantidad de predicciones cargadas por el usuario.
	 */
	public function countPredicciones()
	{
		return ProdePrediccion::model()->count('idProdeUsuario = :id', array(':id' => (int)$this->idProdeUsuario));
	}

	/**
	 * Suma de puntos del usuario en todas las fechas.
	 */
	public function totalPuntos()
	{
		$row = Yii::app()->db->createCommand()
			->select('COALESCE(SUM(puntos), 0) AS total')
			->from('prode_prediccion')
			->where('idProdeUsuario = :id AND puntos IS NOT NULL', array(':id' => (int)$this->idProdeUsuario))
			->queryRow();
		return (int)$row['total'];
	}

	/**
	 * Cantidad de predicciones con puntos = 3 (resultado exacto).
	 */
	public function countExactos()
	{
		return ProdePrediccion::model()->count('idProdeUsuario = :id AND puntos = :p', array(
			':id' => (int)$this->idProdeUsuario,
			':p' => self::PUNTOS_EXACTO,
		));
	}

	/**
	 * Busca por email (case-insensitive). Devuelve null si no existe.
	 */
	public static function findByEmail($email)
	{
		return self::model()->find('LOWER(email) = :e', array(':e' => strtolower(trim($email))));
	}

	/**
	 * Devuelve el nombre del equipo del usuario, o 'Sin equipo' si no tiene.
	 */
	public function getEquipoLabel()
	{
		if ((int)$this->idEquipo > 0 && $this->equipo !== null) {
			return $this->equipo->Nombre;
		}
		return 'Sin equipo';
	}

	/**
	 * Suma de puntos de todos los usuarios activos que pertenecen al equipo
	 * indicado. Devuelve 0 si no hay miembros.
	 */
	public static function totalPuntosEquipo($idEquipo)
	{
		$idEquipo = (int)$idEquipo;
		if ($idEquipo <= 0) return 0;
		$row = Yii::app()->db->createCommand()
			->select('COALESCE(SUM(p.puntos), 0) AS total')
			->from('prode_prediccion p')
			->join('prode_usuario u', 'u.idProdeUsuario = p.idProdeUsuario')
			->where('u.idEquipo = :e AND u.activo = 1 AND p.puntos IS NOT NULL',
				array(':e' => $idEquipo))
			->queryRow();
		return (int)$row['total'];
	}

	/**
	 * Cantidad de usuarios activos en el equipo indicado.
	 */
	public static function countMiembrosEquipo($idEquipo)
	{
		$idEquipo = (int)$idEquipo;
		if ($idEquipo <= 0) return 0;
		return (int)self::model()->count('idEquipo = :e AND activo = 1', array(':e' => $idEquipo));
	}

	/**
	 * Devuelve el listado de equipos con al menos un usuario activo, junto
	 * con la suma de puntos y la cantidad de miembros. Pensado para el
	 * ranking por equipos. Orden: puntos desc, miembros desc, nombre asc.
	 *
	 * @return array Array de arrays con keys: equipo, puntos, miembros.
	 */
	public static function getEquiposConPuntos()
	{
		$sql = "SELECT e.idEquipo, e.Nombre, COUNT(u.idProdeUsuario) AS miembros,
				COALESCE(SUM(p.puntos), 0) AS total
			FROM equipos e
			INNER JOIN prode_usuario u ON u.idEquipo = e.idEquipo AND u.activo = 1
			LEFT JOIN prode_prediccion p ON p.idProdeUsuario = u.idProdeUsuario AND p.puntos IS NOT NULL
			GROUP BY e.idEquipo, e.Nombre
			ORDER BY total DESC, miembros DESC, e.Nombre ASC";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$out = array();
		foreach ($rows as $r) {
			$out[] = array(
				'idEquipo' => (int)$r['idEquipo'],
				'nombre'   => $r['Nombre'],
				'puntos'   => (int)$r['total'],
				'miembros' => (int)$r['miembros'],
			);
		}
		return $out;
	}

	/**
	 * Lista de usuarios activos del equipo, ordenados por puntos desc.
	 * @return array Array de arrays con keys: usuario, puntos, exactos.
	 */
	public static function getMiembrosEquipoConPuntos($idEquipo)
	{
		$idEquipo = (int)$idEquipo;
		if ($idEquipo <= 0) return array();

		$criteria = new CDbCriteria;
		$criteria->condition = 't.idEquipo = :e AND t.activo = 1';
		$criteria->params = array(':e' => $idEquipo);
		$criteria->order = 't.nombre ASC';
		$usuarios = self::model()->findAll($criteria);

		$rows = array();
		foreach ($usuarios as $u) {
			$rows[] = array(
				'usuario' => $u,
				'puntos'  => $u->totalPuntos(),
				'exactos' => $u->countExactos(),
			);
		}
		usort($rows, function($a, $b) {
			if ($a['puntos'] !== $b['puntos']) return $b['puntos'] - $a['puntos'];
			if ($a['exactos'] !== $b['exactos']) return $b['exactos'] - $a['exactos'];
			return strcasecmp($a['usuario']->nombre, $b['usuario']->nombre);
		});
		return $rows;
	}
}
