<?php

/**
 * This is the model class for table "votoencuesta".
 *
 * The followings are the available columns in table 'votoencuesta':
 * @property string $idVotoEncuesta
 * @property string $idEncuesta
 * @property string $email
 * @property string $ip
 * @property string $fecha
 * @property string $hora
 */
class Votoencuesta extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Votoencuesta the static model class
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
		return 'votoencuesta';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idEncuesta', 'required'),
			array('idEncuesta', 'length', 'max'=>10),
			array('email', 'length', 'max'=>200),
			array('ip', 'length', 'max'=>20),
			array('hora', 'length', 'max'=>8),
			array('fecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idVotoEncuesta, idEncuesta, email, ip, fecha, hora', 'safe', 'on'=>'search'),
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
			'idVotoEncuesta' => 'Id Voto Encuesta',
			'idEncuesta' => 'Id Encuesta',
			'email' => 'Email',
			'ip' => 'Ip',
			'fecha' => 'Fecha',
			'hora' => 'Hora',
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

		$criteria->compare('idVotoEncuesta',$this->idVotoEncuesta,true);
		$criteria->compare('idEncuesta',$this->idEncuesta,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('hora',$this->hora,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function ExisteEmail($email){
		$criteria = new CDbCriteria;
		$criteria->condition = "email = :email";
		$criteria->params = array('email' => $email );
		$datos = Votoencuesta::model()->find($criteria);
		
		if (!$datos){
			return false;
		}else{
			return $datos->email == $email;
		}
		
	}

	public static function GrabaVoto($datos){
		$model=new Votoencuesta;

		$model->idEncuesta = $datos['idEncuesta'];
		$model->email = $datos['txtEmail'];
		$model->ip = $_SERVER['REMOTE_ADDR'];
		$model->fecha = date("Ymd");
		$model->hora = date("H:i:s");
		if($model->save()){
			return true;
		}else{
			return false;
		}

	}
}