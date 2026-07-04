<?php

/**
 * This is the model class for table "imagenes".
 *
 * The followings are the available columns in table 'imagenes':
 * @property string $idImagen
 * @property string $Ubicacion
 * @property string $Titulo
 * @property string $Caption
 * @property string $idCategoria
 *
 * The followings are the available model relations:
 * @property Categorias $idCategoria0
 */
class Imagenes extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Imagenes the static model class
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
		return 'imagenes';
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
			array('Ubicacion, Titulo', 'length', 'max'=>200),
			array('idCategoria', 'length', 'max'=>10),
			array('Caption', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idImagen, Ubicacion, Titulo, Caption, idCategoria, Activo', 'safe', 'on'=>'search'),
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
			'idImagen' => 'Id Imagen',
			'Ubicacion' => 'Ubicacion',
			'Titulo' => 'Titulo',
			'Caption' => 'Caption',
			'idCategoria' => 'Id Categoria',
			'Activo' => 'Activo',
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

		$criteria->compare('idImagen',$this->idImagen,true);
		$criteria->compare('Ubicacion',$this->Ubicacion,true);
		$criteria->compare('Titulo',$this->Titulo,true);
		$criteria->compare('Caption',$this->Caption,true);
		$criteria->compare('idCategoria',$this->idCategoria,true);
		$criteria->compare('Activo',$this->Activo);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public static function DevuelveImagenes($idCategoria = 1){
		
		$criteria = new CDbCriteria;
		$criteria->condition='Activo = 1 and idCategoria = :idCategoria';
		$criteria->params = array('idCategoria'=>$idCategoria);
		$criteria->order = 'idImagen desc';
		$criteria->limit = 4;
		$model = Imagenes::model()->findAll($criteria);		
		$array = array();
		
		foreach ($model as $img) {
			$array[] = array('image' => Yii::app()->baseUrl . $img->Ubicacion,
								'label' => $img->Titulo,
								'caption' => '<a href="' . Yii::app()->createUrl('/articulos/view',array('idArticulo'=>$img->idArticulo)) . '">' . $img->Caption . '</a>');
		}
		/*
		$array = array( array('image' => Yii::app()->baseUrl . '/media/imagenes/imagen_portada1.jpg',
				                'label' => 'First Thumbnail label',
				                'caption' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
				            ),
				            array(
				                'image' => Yii::app()->baseUrl . '/media/imagenes/imagen_portada2.jpg',
				                'label' => 'Second Thumbnail label',
				                'caption' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
				            ),
				            array(
				                'image' => Yii::app()->baseUrl . '/media/imagenes/imagen_portada3.jpg',
				                'label' => 'Third Thumbnail label',
				                'caption' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
				            ));*/
		return $array; 				 
		
	}
}