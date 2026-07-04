<?php

/**
 * This is the model class for table "equipostorneo".
 *
 * The followings are the available columns in table 'equipostorneo':
 * @property string $idEquTorneo
 * @property string $idEquipo
 * @property string $idTorneo
 *
 * The followings are the available model relations:
 * @property Equipos $idEquipo0
 * @property Torneo $idTorneo0
 */
class Equipostorneo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Equipostorneo the static model class
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
		return 'equipostorneo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idEquipo, idTorneo', 'required'),
			array('idEquipo, idTorneo', 'length', 'max'=>10),
			array('lista', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idEquTorneo, idEquipo, idTorneo, lista', 'safe', 'on'=>'search'),
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
			'Equipos' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
			'Torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idEquTorneo' => 'Id Equ Torneo',
			'idEquipo' => 'Id Equipo',
			'idTorneo' => 'Id Torneo',
			'lista' => 'Lista de buena fe',
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

		$criteria->compare('idEquTorneo',$this->idEquTorneo,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('lista',$this->lista,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
    public static function getLista($idTorneo, $idEquipo){
        $criteria =new CDbCriteria;
        $criteria->condition = "idTorneo = " . $idTorneo . " and idEquipo = " . $idEquipo;
        $data = Equipostorneo::model()->find($criteria);
        return $data;
    }

}