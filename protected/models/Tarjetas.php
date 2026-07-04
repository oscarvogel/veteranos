<?php

/**
 * This is the model class for table "tarjetas".
 *
 * The followings are the available columns in table 'tarjetas':
 * @property string $idTarjeta
 * @property string $idFixture
 * @property string $idJugador
 * @property integer $Amarilla
 * @property integer $Roja
 * @property string $DesdeFecha
 * @property integer $HastaFecha
 * @property string $Motivo
 *
 * The followings are the available model relations:
 * @property Fixture $idFixture0
 * @property Jugador $idJugador0
 */
class Tarjetas extends CActiveRecord
{
	public $total_amarillas;
	public $ultima_fecha;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tarjetas the static model class
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
		return 'tarjetas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idFixture, idJugador', 'required'),
			array('Amarilla, Roja, HastaFecha', 'numerical', 'integerOnly'=>true),
			array('idFixture, idJugador', 'length', 'max'=>10),
			array('DesdeFecha', 'length', 'max'=>2),
			array('Motivo', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idTarjeta, idFixture, idJugador, Amarilla, Roja, DesdeFecha, HastaFecha, Motivo', 'safe', 'on'=>'search'),
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
			'Fixture' => array(self::BELONGS_TO, 'Fixture', 'idFixture'),
			'Jugador' => array(self::BELONGS_TO, 'Jugador', 'idJugador'),
			'Equipo' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
			'Amarillas' => array(self::BELONGS_TO, 'tarjetas_amarillas', 'idJugador'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idTarjeta' => 'Id Tarjeta',
			'idFixture' => 'Id Fixture',
			'idJugador' => 'Id Jugador',
			'Amarilla' => 'Amarilla',
			'Roja' => 'Roja',
			'DesdeFecha' => 'Desde Fecha',
			'HastaFecha' => 'Hasta Fecha',
			'Motivo' => 'Motivo',
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

		$criteria->compare('idTarjeta',$this->idTarjeta,true);
		$criteria->compare('idFixture',$this->idFixture,true);
		$criteria->compare('idJugador',$this->idJugador,true);
		$criteria->compare('Amarilla',$this->Amarilla);
		$criteria->compare('Roja',$this->Roja);
		$criteria->compare('DesdeFecha',$this->DesdeFecha,true);
		$criteria->compare('HastaFecha',$this->HastaFecha);
		$criteria->compare('Motivo',$this->Motivo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function VerTarjetas($idFixture, $idEquipo){
		$criteria = new CDbCriteria;
		$criteria->join = "inner join jugador on t.idJugador = jugador.idJugador";
		$criteria->condition = "idFixture = " . $idFixture . " and jugador.idEquipo = " . $idEquipo;
		return Tarjetas::model()->findAll($criteria);		
	}


	public function ConsultaTarjetasAmarillas($idTorneo, $Fecha){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idJugador, COUNT(t.amarilla) AS Amarilla";
		$criteria->join = "INNER JOIN fixture ON t.idFixture = fixture.idFixture 
						INNER JOIN jugador ON t.idJugador = jugador.idJugador 
						INNER JOIN equipos ON jugador.idEquipo = equipos.idEquipo";
		$criteria->condition = "fixture.idTorneo = :Torneo AND fixture.Fecha = :Fecha AND t.amarilla = 1";
		$criteria->params = array(":Torneo" => $idTorneo, ":Fecha" => $Fecha);
		$criteria->group = "t.idJugador";
		$criteria->order = "equipos.nombre";
		
		// Crear un comando a partir del criteria para obtener el SQL
		$command = Tarjetas::model()->getCommandBuilder()->createFindCommand(
			Tarjetas::model()->getTableSchema(), 
			$criteria
		);
		
		return Tarjetas::model()->findAll($criteria);        
	}


	public function ConsultaTarjetasAmarillasTorneo($idTorneo){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idJugador, COUNT(t.amarilla) AS Amarilla";
		$criteria->join = "INNER JOIN fixture ON t.idFixture = fixture.idFixture 
						INNER JOIN jugador ON t.idJugador = jugador.idJugador 
						INNER JOIN equipos ON jugador.idEquipo = equipos.idEquipo";
		$criteria->condition = "fixture.idTorneo = :Torneo AND t.amarilla = 1";
		$criteria->params = array(":Torneo" => $idTorneo);
		$criteria->group = "t.idJugador";
		$criteria->order = "equipos.nombre";
		
		// Crear un comando a partir del criteria para obtener el SQL
		$command = Tarjetas::model()->getCommandBuilder()->createFindCommand(
			Tarjetas::model()->getTableSchema(), 
			$criteria
		);
		
		return Tarjetas::model()->findAll($criteria);        
	}

	public function ConsultaTarjetasRojas($idTorneo, $Fecha){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idJugador, Roja, DesdeFecha, HastaFecha, Motivo";
		$criteria->join = "inner join fixture on t.idFixture = fixture.idFixture 
					inner join jugador on t.idJugador = jugador.idJugador 
					inner join equipos on jugador.idEquipo = equipos.idEquipo";
		$criteria->condition = "fixture.idTorneo = :Torneo and Roja = 1 and Fecha = :Fecha";
		$criteria->params = array("Torneo" => $idTorneo, "Fecha" => $Fecha);
		$criteria->group = "t.idJugador";
		$criteria->order = "equipos.nombre";
		return Tarjetas::model()->findAll($criteria);		
	}

	public function ConsultaTarjetasJugador($idJugador, $amarilla){
		$criteria = new CDbCriteria;
		if($amarilla){
			$criteria->condition = "amarilla = 1 and idJugador = " . $idJugador;	
		}else{
			$criteria->condition = "roja = 1 and idJugador = " . $idJugador;
		}
		
		return Tarjetas::model()->findAll($criteria);
	}
	
	public function TarjetasJugador($idJugador){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idJugador, count(t.amarilla) as Amarilla";
		$criteria->condition = "idJugador = :idJugador and amarilla=1 and year(f.fecha) = year(curdate())";
		$criteria->join = "inner join fixture f on t.idFixture = f.idFixture";
		$criteria->params = array("idJugador"=>$idJugador);
		$criteria->group = "t.idJugador";
		
		$dato = Tarjetas::model()->find($criteria);
		
		return $dato->Amarilla;
	}
	
	public function TarjetasJugadorTorneo($idJugador, $torneo){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idJugador, count(t.amarilla) as Amarilla";
		$criteria->condition = "idJugador = :idJugador and amarilla=1 and idTorneo = :idTorneo";
		$criteria->join = "inner join fixture f on t.idFixture = f.idFixture";
		$criteria->params = array("idJugador"=>$idJugador, 'idTorneo' => $torneo);
		$criteria->group = "t.idJugador";
		
		$dato = Tarjetas::model()->find($criteria);
		return $dato->Amarilla;
	}	
	
	public function TarjetasEquipo($idTorneo, $idEquipo, $tipo, $torneo){
		$criteria = new CDbCriteria;
		$criteria->select = "distinct t.idJugador, t.Amarilla, t.Roja, t.DesdeFecha, t.HastaFecha, t.Motivo";
		if($torneo->Estado == 'F'){
			$criteria->join = "inner join fixture on t.idFixture = fixture.idFixture inner join historicojugador on t.idJugador = historicojugador.idJugador";
			$criteria->condition = "fixture.idTorneo = :idTorneo and historicojugador.idEquipo = :idEquipo ";
		}else{
			#$criteria->join = "inner join fixture on t.idFixture = fixture.idFixture inner join jugador on t.idJugador = jugador.idJugador";
            $criteria->join = "inner join fixture on t.idFixture = fixture.idFixture";
			$criteria->condition = "year(fixture.fecha) = year(curdate()) and t.idEquipo = :idEquipo ";
		}
		if($tipo == 'A'){
			$criteria->condition .= " and t.amarilla = 1";
		}else{
			$criteria->condition .= " and t.roja = 1";
		}
		$criteria->params = array('idEquipo' => $idEquipo);
		return Tarjetas::model()->findAll($criteria);
	}
	
	public function FairPlay($idTorneo = 1){
		$criteria = new CDbCriteria;
		$criteria->select = "t.idEquipo, count(if(t.Amarilla=1,1,null)) as Amarilla, count(IF(t.Roja=1,1,null)) as Roja, Count(amarilla) as Total";
		$criteria->join = "inner join fixture f on t.idFixture = f.idFixture";
		$torneos = Paramsist::getTorneosRelacionados($idTorneo);
		$criteria->condition = "f.PostTemporada = 0 and f.idTorneo in(" . $torneos .")";
		$criteria->group = "t.idEquipo";
		$criteria->order = "Total";
		
		return Tarjetas::model()->findAll($criteria);		
	}
}