<?php

/**
 * Prediccion de un partido del prode.
 * Unica por (idProdeUsuario, idFixture).
 *
 * @property integer $idProdePrediccion
 * @property integer $idProdeUsuario
 * @property integer $idFixture
 * @property integer $golesLocal
 * @property integer $golesVisitante
 * @property integer $puntos
 * @property string $createdAt
 * @property string $updatedAt
 */
class ProdePrediccion extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'prode_prediccion';
	}

	public function rules()
	{
		return array(
			array('idProdeUsuario, idFixture, golesLocal, golesVisitante', 'required'),
			array('idProdeUsuario, idFixture, golesLocal, golesVisitante, puntos', 'numerical', 'integerOnly'=>true, 'min'=>0),
			array('golesLocal, golesVisitante', 'length', 'max'=>3),
			array('createdAt, updatedAt', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'usuario' => array(self::BELONGS_TO, 'ProdeUsuario', 'idProdeUsuario'),
			'partido' => array(self::BELONGS_TO, 'Fixture', 'idFixture'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'idProdePrediccion' => 'Id',
			'idProdeUsuario' => 'Usuario',
			'idFixture' => 'Partido',
			'golesLocal' => 'Goles local',
			'golesVisitante' => 'Goles visitante',
			'puntos' => 'Puntos',
			'createdAt' => 'Creado',
			'updatedAt' => 'Actualizado',
		);
	}

	protected function beforeSave()
	{
		if (parent::beforeSave()) {
			$now = date('Y-m-d H:i:s');
			if ($this->isNewRecord) {
				$this->createdAt = $now;
			}
			$this->updatedAt = $now;
			return true;
		}
		return false;
	}

	/**
	 * Calcula los puntos de esta prediccion contra un resultado real.
	 * Devuelve null si el partido no tiene goles cargados.
	 */
	public function calcularPuntos($golesLocalReal, $golesVisitanteReal)
	{
		if ($golesLocalReal === null || $golesVisitanteReal === null) {
			return null;
		}
		$golesLocalReal = (int)$golesLocalReal;
		$golesVisitanteReal = (int)$golesVisitanteReal;

		// Resultado exacto
		if ((int)$this->golesLocal === $golesLocalReal && (int)$this->golesVisitante === $golesVisitanteReal) {
			return ProdeUsuario::PUNTOS_EXACTO;
		}
		// Solo signo (ganador / empate) coincide. Empate cuenta como signo.
		$signoReal = $this->signo($golesLocalReal, $golesVisitanteReal);
		$signoPred = $this->signo((int)$this->golesLocal, (int)$this->golesVisitante);
		if ($signoPred === $signoReal) {
			return ProdeUsuario::PUNTOS_SIGNO;
		}
		return 0;
	}

	/**
	 * Devuelve el signo: -1 visitante, 0 empate, 1 local.
	 */
	public function signo($golesLocal, $golesVisitante)
	{
		if ($golesLocal > $golesVisitante) return 1;
		if ($golesLocal < $golesVisitante) return -1;
		return 0;
	}
}
