<?php

/**
 * This is the model class for table "Articulos".
 *
 * The followings are the available columns in table 'Articulos':
 * @property string $idArticulo
 * @property string $FechaPublicacion
 * @property integer $Activo
 * @property string $Titulo
 * @property string $Texto
 */
class Articulos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Articulos the static model class
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
		return 'Articulos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Activo', 'numerical', 'integerOnly'=>true),
			array('Titulo', 'length', 'max'=>200),
			array('FechaPublicacion, Texto, Introduccion', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idArticulo, FechaPublicacion, Activo, Titulo, Introduccion, Texto', 'safe', 'on'=>'search'),
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
			'idArticulo' => 'Id Articulo',
			'FechaPublicacion' => 'Fecha Publicacion',
			'Activo' => 'Activo',
			'Titulo' => 'Titulo',
			'Texto' => 'Texto',
			'Introduccion' => 'Texto Introductorio',
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

		$criteria->compare('idArticulo',$this->idArticulo,true);
		$criteria->compare('FechaPublicacion',$this->FechaPublicacion,true);
		$criteria->compare('Activo',$this->Activo);
		$criteria->compare('Titulo',$this->Titulo,true);
		$criteria->compare('Texto',$this->Texto,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}