<?php

/**
 * This is the model class for table "opcionesencuesta".
 *
 * The followings are the available columns in table 'opcionesencuesta':
 * @property string $idOpcionEncuesta
 * @property string $idEncuesta
 * @property string $Opcion
 * @property string $Votos
 */
class Opcionesencuesta extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Opcionesencuesta the static model class
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
		return 'opcionesencuesta';
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
			array('idEncuesta, Votos', 'length', 'max'=>10),
			array('Opcion', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idOpcionEncuesta, idEncuesta, Opcion, Votos', 'safe', 'on'=>'search'),
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
			'encuesta' => array(self::BELONGS_TO, 'Encuestas', 'idEncuesta'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idOpcionEncuesta' => 'Id Opcion Encuesta',
			'idEncuesta' => 'Id Encuesta',
			'Opcion' => 'Opcion',
			'Votos' => 'Votos',
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

		$criteria->compare('idOpcionEncuesta',$this->idOpcionEncuesta,true);
		$criteria->compare('idEncuesta',$this->idEncuesta,true);
		$criteria->compare('Opcion',$this->Opcion,true);
		$criteria->compare('Votos',$this->Votos,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function ObtienePorcentaje($idEncuesta, $idOpcionEncuesta){
		$criteria = new CDbCriteria;
		$criteria->select = "sum(votos) as Votos";
		$criteria->condition = "idEncuesta = :idEncuesta";
		$criteria->group = "idEncuesta";
		$criteria->params = array('idEncuesta' => $idEncuesta);
		$datoTotal = Opcionesencuesta::model()->find($criteria);

		$criteria = new CDbCriteria;
		$criteria->select = "votos as Votos";
		$criteria->condition = "idEncuesta = :idEncuesta and idOpcionEncuesta = :idOpcionEncuesta";
		$criteria->params = array('idEncuesta' => $idEncuesta, 'idOpcionEncuesta' => $idOpcionEncuesta);
		$datoEncuesta = Opcionesencuesta::model()->find($criteria);

		if($datoTotal->Votos == 0){
			return 100;
		}else{
			return $datoEncuesta->Votos / $datoTotal->Votos * 100;
		}
	}

	public static function SumaVoto($idOpcionEncuesta){
		$model = Opcionesencuesta::model()->findByPk($idOpcionEncuesta);
		$model->Votos ++;
		$model->save();
	}

	public static function TotalVotantes($idEncuesta){
		$criteria = new CDbCriteria;
		$criteria->select = "sum(votos) as Votos";
		$criteria->condition = "idEncuesta = :idEncuesta";
		$criteria->group = "idEncuesta";
		$criteria->params = array('idEncuesta' => $idEncuesta);
		$datoTotal = Opcionesencuesta::model()->find($criteria);

		return $datoTotal->Votos;
	}
}