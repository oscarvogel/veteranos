<?php

/**
 * This is the model class for table "posicionjugador".
 *
 * The followings are the available columns in table 'posicionjugador':
 * @property string $idPosicion
 * @property string $Detalle
 *
 * The followings are the available model relations:
 * @property Plantel[] $plantels
 */
class Posicionjugador extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Posicionjugador the static model class
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
		return 'posicionjugador';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Detalle', 'required'),
			array('Detalle', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idPosicion, Detalle', 'safe', 'on'=>'search'),
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
			'plantels' => array(self::HAS_MANY, 'Plantel', 'Posicion'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idPosicion' => 'Id Posicion',
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

		$criteria->compare('idPosicion',$this->idPosicion,true);
		$criteria->compare('Detalle',$this->Detalle,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getListPosiciones(){
		return CHtml::listData(Posicionjugador::model()->findAll(array('order'=>'Detalle')),'idPosicion','Detalle');
	}

}
