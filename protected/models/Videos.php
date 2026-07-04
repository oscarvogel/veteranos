<?php

/**
 * This is the model class for table "videos".
 *
 * The followings are the available columns in table 'videos':
 * @property string $idVideo
 * @property string $Nombre
 * @property string $Video
 * @property string $idCategoria
 *
 * The followings are the available model relations:
 * @property Categorias $idCategoria0
 */
class Videos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Videos the static model class
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
		return 'videos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idCategoria', 'required'),
			array('Nombre', 'length', 'max'=>100),
			array('Video', 'length', 'max'=>200),
			array('idCategoria', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idVideo, Nombre, Video, idCategoria', 'safe', 'on'=>'search'),
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
			'idCategoria0' => array(self::BELONGS_TO, 'Categorias', 'idCategoria'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idVideo' => 'Id Video',
			'Nombre' => 'Nombre',
			'Video' => 'Video',
			'idCategoria' => 'Id Categoria',
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

		$criteria->compare('idVideo',$this->idVideo,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Video',$this->Video,true);
		$criteria->compare('idCategoria',$this->idCategoria,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}