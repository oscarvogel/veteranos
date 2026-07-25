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
			array('activo, esAdmin', 'numerical', 'integerOnly'=>true),
			array('createdAt, lastLogin', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'predicciones' => array(self::HAS_MANY, 'ProdePrediccion', 'idProdeUsuario'),
			'resetTokens' => array(self::HAS_MANY, 'ProdeResetToken', 'idProdeUsuario'),
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
}
