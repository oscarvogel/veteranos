<?php

/**
 * This is the model class for table "paramsist".
 *
 * The followings are the available columns in table 'paramsist':
 * @property string $parametro
 * @property string $valor
 */
class Paramsist extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Paramsist the static model class
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
		return 'paramsist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parametro, valor', 'required'),
			array('parametro', 'length', 'max'=>60),
			array('valor', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('parametro, valor', 'safe', 'on'=>'search'),
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
			'parametro' => 'Parametro',
			'valor' => 'Valor',
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

		$criteria->compare('parametro',$this->parametro,true);
		$criteria->compare('valor',$this->valor,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getTorneosRelacionados($idTorneo){
		if ($idTorneo == 25 or $idTorneo == 28 or $idTorneo == 29 or $idTorneo == 30){
			$torneos = '25,28,29,30';
		}elseif (($idTorneo == 31 or $idTorneo == 32 or $idTorneo == 35 or $idTorneo == 36)) {
			$torneos = '31,32,35,36';
		}elseif (($idTorneo == 37 or $idTorneo == 40)) {
			$torneos = '37,40';
		}elseif (($idTorneo == 41 or $idTorneo == 42)) {
			$torneos = '41,42';
		}elseif (($idTorneo == 45 or $idTorneo == 46)) {
			$torneos = '45,46';
		}elseif (($idTorneo == 49 or $idTorneo == 50)) {
			$torneos = '49,50';
		}elseif (($idTorneo == 53 or $idTorneo == 54)) {
			$torneos = '53,54';
		}elseif (($idTorneo == 57 or $idTorneo == 58)) {
			$torneos = '57,58';
        }else{
			$torneos = $idTorneo;
		}
		return $torneos;
	}

}