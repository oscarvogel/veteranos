<?php

/**
 * This is the model class for table "posiciones".
 *
 * The followings are the available columns in table 'posiciones':
 * @property integer $idPosiciones
 * @property string $idTorneo
 * @property string $PJ
 * @property string $idEquipo
 * @property string $Posicion
 * @property string $Puntos
 * @property string $PG
 * @property string $PE
 * @property string $PP
 * @property string $GF
 * @property string $GC
 *
 * The followings are the available model relations:
 * @property Torneo $idTorneo0
 * @property Equipos $idEquipo0
 */
class Posiciones extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Posiciones the static model class
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
		return 'posiciones';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idTorneo, idEquipo', 'length', 'max'=>10),
			array('PJ, Posicion', 'length', 'max'=>2),
			array('Puntos, PG, PE, PP, GF, GC', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idPosiciones, idTorneo, PJ, idEquipo, Posicion, Puntos, PG, PE, PP, GF, GC', 'safe', 'on'=>'search'),
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
			'torneo' => array(self::BELONGS_TO, 'Torneo', 'idTorneo'),
			'equipo' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idPosiciones' => 'Id Posiciones',
			'idTorneo' => 'Id Torneo',
			'PJ' => 'Pj',
			'idEquipo' => 'Id Equipo',
			'Posicion' => 'Posicion',
			'Puntos' => 'Puntos',
			'PG' => 'Pg',
			'PE' => 'Pe',
			'PP' => 'Pp',
			'GF' => 'Gf',
			'GC' => 'Gc',
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

		$criteria->compare('idPosiciones',$this->idPosiciones);
		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('PJ',$this->PJ,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('Posicion',$this->Posicion,true);
		$criteria->compare('Puntos',$this->Puntos,true);
		$criteria->compare('PG',$this->PG,true);
		$criteria->compare('PE',$this->PE,true);
		$criteria->compare('PP',$this->PP,true);
		$criteria->compare('GF',$this->GF,true);
		$criteria->compare('GC',$this->GC,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getTabla( $idTorneo = 1){
		$criteria = new CDbCriteria;
		$criteria->condition = 'idTorneo=:idTorneo';
		$criteria->order = 'Posicion';
		$criteria->params = array('idTorneo'=>$idTorneo);
		
		return Posiciones::model()->findAll($criteria);
	}

}