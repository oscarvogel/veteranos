<?php

/**
 * This is the model class for table "plantel".
 *
 * The followings are the available columns in table 'plantel':
 * @property string $idPlantel
 * @property string $Nombre
 * @property string $FechaNacimiento
 * @property string $FechaIngreso
 * @property string $FechaSalida
 * @property string $Datos
 * @property string $Fotografia
 */
class Plantel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Plantel the static model class
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
		return 'plantel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre, Apodo, UltimoClub', 'length', 'max'=>50),
			array('LugarNacimiento', 'length', 'max'=>100),
			array('Fotografia', 'length', 'max'=>200),
			array('Altura', 'length', 'max'=>12),
			array('Posicion', 'length', 'max'=>10),
			array('PiernaHabil', 'length', 'max'=>30),
			array('FechaNacimiento, FechaIngreso, FechaSalida, Datos', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idPlantel, Nombre, FechaNacimiento, FechaIngreso, FechaSalida, Datos, Posicion, Fotografia,
				PiernaHabil, Altura, LugarNacimiento, Apodo, UltimoClub', 'safe', 'on'=>'search'),
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
			'posicion' => array(self::BELONGS_TO, 'Posicionjugador', 'Posicion'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idPlantel' => 'Id Plantel',
			'Nombre' => 'Nombre',
			'FechaNacimiento' => 'Fecha Nacimiento',
			'FechaIngreso' => 'Fecha Ingreso',
			'FechaSalida' => 'Fecha Salida',
			'Datos' => 'Datos',
			'Fotografia' => 'Fotografia',
			'Posicion' => 'Posicion',
			'LugarNacimiento' => 'Lugar Nacimiento',
			'Altura' => 'Altura',
			'Apodo' => 'Apodo',
			'PiernaHabil' => 'Pierna Habil',
			'UltimoClub' => 'Ultimo Club',
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

		$criteria->compare('idPlantel',$this->idPlantel,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('FechaNacimiento',$this->FechaNacimiento,true);
		$criteria->compare('FechaIngreso',$this->FechaIngreso,true);
		$criteria->compare('FechaSalida',$this->FechaSalida,true);
		$criteria->compare('Datos',$this->Datos,true);
		$criteria->compare('Fotografia',$this->Fotografia,true);
		$criteria->compare('Posicion',$this->Posicion,true);
		$criteria->compare('LugarNacimiento',$this->LugarNacimiento,true);
		$criteria->compare('Altura',$this->Altura,true);
		$criteria->compare('Apodo',$this->Apodo,true);
		$criteria->compare('PiernaHabil',$this->PiernaHabil,true);
		$criteria->compare('UltimoClub',$this->UltimoClub,true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	
	public static function getJugadorAleatorio(){
		$criteria = new CDbCriteria;
		$criteria->order = "rand()";
		$criteria->limit = 1;
		
		return Plantel::model()->find($criteria);
	}


	public static function getPlantelCompleto(){
		$criteria = new CDbCriteria;
		$criteria->order = "Posicion";
		
		return Plantel::model()->findAll($criteria);
		
	}
}