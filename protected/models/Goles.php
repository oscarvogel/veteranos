<?php

/**
 * This is the model class for table "goles".
 *
 * The followings are the available columns in table 'goles':
 * @property string $idGol
 * @property string $idJugador
 * @property string $idFixture
 * @property integer $Cantidad
 *
 * The followings are the available model relations:
 * @property Fixture $idFixture0
 * @property Jugador $idJugador0
 */
class Goles extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Goles the static model class
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
		return 'goles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idJugador, idFixture', 'required'),
			array('Cantidad', 'numerical', 'integerOnly'=>true),
			array('idJugador, idFixture', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idGol, idJugador, idFixture, Cantidad', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idGol' => 'Id Gol',
			'idJugador' => 'Id Jugador',
			'idFixture' => 'Id Fixture',
			'Cantidad' => 'Cantidad',
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

		$criteria->compare('idGol',$this->idGol,true);
		$criteria->compare('idJugador',$this->idJugador,true);
		$criteria->compare('idFixture',$this->idFixture,true);
		$criteria->compare('Cantidad',$this->Cantidad);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function verGolesFixture($idFixture){
		$criteria = new CDbCriteria;
		$criteria->condition = "idFixture = " . $idFixture;
		
		return Goles::model()->findAll($criteria);
	}
	
	public function Goleadores($idTorneo){
		$criteria = new CDbCriteria;
		$criteria->select = "fixture.idTorneo, idJugador, sum(Cantidad) as Cantidad";
		$criteria->group = "idJugador";
		$criteria->order = "Cantidad desc";
		$criteria->join = "inner join fixture on t.idFixture = fixture.idFixture";
		$torneos = Paramsist::getTorneosRelacionados($idTorneo);

		$criteria->condition = "fixture.idTorneo in (" . $torneos . ") and PostTemporada = 0";
		
		return Goles::model()->findAll($criteria); 
	}
	
	
	public function GolesJugador($idJugador, $idTorneo){
		$criteria = new CDbCriteria;
		$criteria->join = "inner join fixture on t.idFixture = fixture.idFixture";
		$torneos = Paramsist::getTorneosRelacionados($idTorneo);
		$criteria->condition = "fixture.idTorneo in (" . $torneos . ") and t.idJugador = " . $idJugador . " and PostTemporada = 0";
		
		return Goles::model()->findAll($criteria); 
	}
	
	public function golPartido($idFixture,$idEquipo,$torneo){
		$criteria = new CDbCriteria;
		if($torneo->Estado == 'F'){
			$criteria->condition = "idFixture = :idFixture and historicojugador.idEquipo = :idEquipo";
			$criteria->join = "inner join historicojugador on historicojugador.idJugador = t.idJugador";
			$criteria->params = array("idFixture" => $idFixture, "idEquipo" => $idEquipo);
		}else{
			$criteria->condition = "idFixture = :idFixture and jugador.idEquipo = :idEquipo";
			$criteria->join = "inner join jugador on t.idJugador = jugador.idJugador";
			$criteria->params = array("idFixture" => $idFixture, "idEquipo" => $idEquipo);
		}
		$goles = Goles::model()->findAll($criteria);
		$datos = "";
		foreach($goles as $gol){
			$datos .= $gol->Jugador->Nombre . ' ' . $gol->Cantidad . '<br>';
		}
		return $datos;
	}
	
	public function golJugador($idJugador){
		$criteria= new CDbCriteria;
		$criteria->condition 	= "idJugador = :idJugador";
		$criteria->params		= array('idJugador' => $idJugador);
		
		return Goles::model()->findAll($criteria);
	}
}	