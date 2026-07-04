<?php

/**
 * This is the model class for table "efemerides".
 *
 * The followings are the available columns in table 'efemerides':
 * @property string $idEfemerides
 * @property string $Fecha
 * @property string $Titulo
 * @property string $Detalle
 */
class Efemerides extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Efemerides the static model class
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
		return 'efemerides';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Titulo', 'length', 'max'=>100),
			array('Fecha, Detalle', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idEfemerides, Fecha, Titulo, Detalle', 'safe', 'on'=>'search'),
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
			'idEfemerides' => 'Id Efemerides',
			'Fecha' => 'Fecha',
			'Titulo' => 'Titulo',
			'Detalle' => 'Detalle',
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

		$criteria->compare('idEfemerides',$this->idEfemerides,true);
		$criteria->compare('Fecha',$this->Fecha,true);
		$criteria->compare('Titulo',$this->Titulo,true);
		$criteria->compare('Detalle',$this->Detalle,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getEfemeridesMes(){
		$criteria = new CDbCriteria;
		$criteria->condition = 'month(fecha) = month(curdate())';
		$criteria->order = 'fecha';
		$model = Efemerides::model()->findAll($criteria);		
		
		return $model;
	}
}