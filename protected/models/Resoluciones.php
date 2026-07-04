<?php

/**
 * This is the model class for table "resoluciones".
 *
 * The followings are the available columns in table 'resoluciones':
 * @property string $idResolucion
 * @property string $Fecha
 * @property string $URL
 */
class Resoluciones extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Resoluciones the static model class
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
		return 'resoluciones';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('URL', 'length', 'max'=>200),
			array('Fecha, Detalle', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idResolucion, Detalle, Fecha, URL', 'safe', 'on'=>'search'),
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
			'idResolucion' => 'Id Resolucion',
			'Detalle' => 'Detalle', 
			'Fecha' => 'Fecha',
			'URL' => 'Url',
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

		$criteria->compare('idResolucion',$this->idResolucion,true);
		$criteria->compare('Detalle',$this->Detalle,true);
		$criteria->compare('Fecha',$this->Fecha,true);
		$criteria->compare('URL',$this->URL,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}