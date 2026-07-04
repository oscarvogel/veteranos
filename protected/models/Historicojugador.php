<?php

/**
 * This is the model class for table "historicojugador".
 *
 * The followings are the available columns in table 'historicojugador':
 * @property string $idHistoricoJugador
 * @property string $idEquipo
 * @property string $idTorneo
 * @property string $idJugador
 *
 * The followings are the available model relations:
 * @property Equipos $idEquipo0
 * @property Torneo $idTorneo0
 * @property Jugador $idJugador0
 */
class Historicojugador extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Historicojugador the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'historicojugador';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idEquipo, idTorneo, idJugador', 'required'),
			array('idEquipo, idTorneo, idJugador', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idHistoricoJugador, idEquipo, idTorneo, idJugador', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'Equipo' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
			'Torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
			'Jugador' => array(self::BELONGS_TO, 'Jugador', 'idJugador'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idHistoricoJugador' => 'Id Historico Jugador',
			'idEquipo' => 'Id Equipo',
			'idTorneo' => 'Id Torneo',
			'idJugador' => 'Id Jugador',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idHistoricoJugador',$this->idHistoricoJugador,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('idJugador',$this->idJugador,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function ConsultaEquiposJugador($idJugador){
		$criteria=new CDbCriteria;
		$criteria->join = "inner join torneo on t.idTorneo = torneo.idTorneo inner join equipos on t.idEquipo = equipos.idEquipo";
		$criteria->condition = "t.idJugador=:idJugador";
		$criteria->params = array('idJugador'=>$idJugador);
		
		return Historicojugador::model()->findAll($criteria);
	}
	
	public function EquipoJugador($idTorneo, $idJugador){
		$criteria=new CDbCriteria;
		$criteria->condition = "t.idJugador = :idJugador and t.idTorneo = :idTorneo";
		$criteria->params = array('idJugador'=>$idJugador, 'idTorneo'=>$idTorneo);
		
		$dato = Historicojugador::model()->find($criteria);
		
		return $dato->idEquipo;
	}	
}