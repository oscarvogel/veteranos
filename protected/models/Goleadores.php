<?php

/**
 * This is the model class for table "goleadores".
 *
 * The followings are the available columns in table 'goleadores':
 * @property string $idGoleador
 * @property string $Nombre
 * @property string $Goles
 * @property string $idTorneo
 *
 * The followings are the available model relations:
 * @property Torneo $idTorneo0
 */
class Goleadores extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Goleadores the static model class
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
		return 'goleadores';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idTorneo', 'required'),
			array('Nombre, Goles', 'length', 'max'=>50),
			array('idTorneo', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idGoleador, Nombre, Goles, idTorneo', 'safe', 'on'=>'search'),
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
			'torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idGoleador' => 'Id Goleador',
			'Nombre' => 'Nombre',
			'Goles' => 'Goles',
			'idTorneo' => 'Id Torneo',
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

		$criteria->compare('idGoleador',$this->idGoleador,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Goles',$this->Goles,true);
		$criteria->compare('idTorneo',$this->idTorneo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getGoleadores($idTorneo = 1){
		$criteria = new CDbCriteria;

		$criteria->condition = "idTorneo = :idTorneo";
		$criteria->params = array('idTorneo' => $idTorneo);

		return Goleadores::model()->findAll($criteria);
	}
}