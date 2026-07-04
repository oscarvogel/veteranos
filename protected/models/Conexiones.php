<?php

/**
 * This is the model class for table "conexiones".
 *
 * The followings are the available columns in table 'conexiones':
 * @property string $idConexion
 * @property string $latitud
 * @property string $longitud
 * @property string $altitud
 * @property string $horario
 */
class Conexiones extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Conexiones the static model class
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
		return 'conexiones';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('latitud, longitud, altitud, horario', 'required'),
			array('latitud, longitud, altitud, horario', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idConexion, latitud, longitud, altitud, horario', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idConexion' => 'Id Conexion',
			'latitud' => 'Latitud',
			'longitud' => 'Longitud',
			'altitud' => 'Altitud',
			'horario' => 'Horario',
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

		$criteria->compare('idConexion',$this->idConexion,true);
		$criteria->compare('latitud',$this->latitud,true);
		$criteria->compare('longitud',$this->longitud,true);
		$criteria->compare('altitud',$this->altitud,true);
		$criteria->compare('horario',$this->horario,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}