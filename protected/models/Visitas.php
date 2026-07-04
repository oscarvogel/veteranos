<?php

/**
 * This is the model class for table "visitas".
 *
 * The followings are the available columns in table 'visitas':
 * @property string $idVisita
 * @property string $Nombre
 * @property string $fecha
 * @property string $hora
 * @property string $IP
 * @property string $Comentario
 */
class Visitas extends CActiveRecord
{
	public $verifyCode;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Visitas the static model class
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
		return 'visitas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre', 'length', 'max'=>50),
			array('IP', 'length', 'max'=>15),
			array('fecha, Comentario, hora', 'safe'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idVisita, Nombre, fecha, hora, IP, Comentario', 'safe', 'on'=>'search'),
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
			'idVisita' => 'Id Visita',
			'Nombre' => 'Nombre',
			'fecha' => 'Fecha',
			'hora' => 'Hora',
			'IP' => 'Ip',
			'Comentario' => 'Comentario',
			'verifyCode'=>Yii::t('app','Verification Code'),
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

		$criteria->compare('idVisita',$this->idVisita,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('hora',$this->hora,true);
		$criteria->compare('IP',$this->IP,true);
		$criteria->compare('Comentario',$this->Comentario,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public static function UltimasVisitas(){
		$criteria = new CDbCriteria;
		$criteria->limit = 10;
		$criteria->order = "idVisita desc";
		
		return Visitas::model()->findAll($criteria);
	}
}