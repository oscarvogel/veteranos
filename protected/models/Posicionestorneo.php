<?php

/**
 * This is the model class for table "posicionestorneo".
 *
 * The followings are the available columns in table 'posicionestorneo':
 * @property string $idPosicionTorneo
 * @property string $idTorneo
 * @property string $idEquipo
 * @property string $Posicion
 *
 * The followings are the available model relations:
 * @property Equipos $idEquipo0
 * @property Torneo $idTorneo0
 */
class Posicionestorneo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Posicionestorneo the static model class
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
		return 'posicionestorneo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idTorneo, idEquipo', 'required'),
			array('idTorneo, idEquipo', 'length', 'max'=>10),
			array('Posicion', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idPosicionTorneo, idTorneo, idEquipo, Posicion', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idPosicionTorneo' => 'Id Posicion Torneo',
			'idTorneo' => 'Id Torneo',
			'idEquipo' => 'Id Equipo',
			'Posicion' => 'Posicion',
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

		$criteria->compare('idPosicionTorneo',$this->idPosicionTorneo,true);
		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('Posicion',$this->Posicion,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function PosicionFinal($idTorneo){
		$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = :idTorneo";
		$criteria->params = array('idTorneo'=>$idTorneo);
		
		return Posicionestorneo::model()->findAll($criteria);
	}
}