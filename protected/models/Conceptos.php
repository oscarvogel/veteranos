<?php

/**
 * This is the model class for table "conceptos".
 *
 * The followings are the available columns in table 'conceptos':
 * @property string $idConcepto
 * @property string $Nombre
 * @property string $Monto
 *
 * The followings are the available model relations:
 * @property Ingresos[] $ingresoses
 */
class Conceptos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Conceptos the static model class
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
		return 'conceptos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre', 'length', 'max'=>100),
			array('Monto', 'length', 'max'=>12),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idConcepto, Nombre, Monto', 'safe', 'on'=>'search'),
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
			'ingresoses' => array(self::HAS_MANY, 'Ingresos', 'idConcepto'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idConcepto' => 'Id Concepto',
			'Nombre' => 'Nombre',
			'Monto' => 'Monto',
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

		$criteria->compare('idConcepto',$this->idConcepto,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Monto',$this->Monto,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getListConceptos(){
		return CHtml::listData(Conceptos::model()->findAll(array('order'=>'Nombre')),'idConcepto','Nombre');
	}

}
