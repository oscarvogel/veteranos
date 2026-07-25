<?php

/**
 * Marca de "publicada" para una (idTorneo, NFecha) del prode.
 * Cuando una fecha esta publicada, el ranking y los resultados son
 * visibles para todos.
 *
 * @property integer $idProdeFecha
 * @property integer $idTorneo
 * @property integer $NFecha
 * @property string $publicadaEn
 * @property integer $publicadaPor
 */
class ProdeFechaPublicada extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'prode_fecha_publicada';
	}

	public function rules()
	{
		return array(
			array('idTorneo, NFecha, publicadaEn', 'required'),
			array('idTorneo, NFecha, publicadaPor', 'numerical', 'integerOnly'=>true),
			array('publicadaEn', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'idProdeFecha' => 'Id',
			'idTorneo' => 'Torneo',
			'NFecha' => 'Nº Fecha',
			'publicadaEn' => 'Publicada en',
			'publicadaPor' => 'Publicada por',
		);
	}

	/**
	 * Devuelve true si la fecha del torneo esta publicada.
	 */
	public static function estaPublicada($idTorneo, $nFecha)
	{
		return self::model()->exists('idTorneo = :t AND NFecha = :n', array(
			':t' => (int)$idTorneo,
			':n' => (int)$nFecha,
		));
	}

	public static function marcarPublicada($idTorneo, $nFecha, $idUsuarioAdmin = null)
	{
		$existente = self::model()->find('idTorneo = :t AND NFecha = :n', array(
			':t' => (int)$idTorneo,
			':n' => (int)$nFecha,
		));
		if ($existente) {
			$existente->publicadaEn = date('Y-m-d H:i:s');
			if ($idUsuarioAdmin !== null) {
				$existente->publicadaPor = (int)$idUsuarioAdmin;
			}
			$existente->save();
		} else {
			$nuevo = new self;
			$nuevo->idTorneo = (int)$idTorneo;
			$nuevo->NFecha = (int)$nFecha;
			$nuevo->publicadaEn = date('Y-m-d H:i:s');
			$nuevo->publicadaPor = $idUsuarioAdmin !== null ? (int)$idUsuarioAdmin : null;
			$nuevo->save();
		}
	}
}
