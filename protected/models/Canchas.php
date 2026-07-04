<?php

/**
 * This is the model class for table "canchas".
 *
 * The followings are the available columns in table 'canchas':
 * @property string $idCancha
 * @property string $Nombre
 * @property string $Titular
 * @property string $Telefono
 */
class Canchas extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Canchas the static model class
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
		return 'canchas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre, Titular, Telefono', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idCancha, Nombre, Titular, Telefono', 'safe', 'on'=>'search'),
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
			'idCancha' => 'Id Cancha',
			'Nombre' => 'Nombre',
			'Titular' => 'Dueño',
			'Telefono' => 'Telefono',
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

		$criteria->compare('idCancha',$this->idCancha,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Titular',$this->Titular,true);
		$criteria->compare('Telefono',$this->Telefono,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getListCancha(){
		return CHtml::listData(Canchas::model()->findAll(array('order'=>'Nombre')),'idCancha','Nombre');
	}
	
}