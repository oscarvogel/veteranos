<?php

/**
 * This is the model class for table "egresos".
 *
 * The followings are the available columns in table 'egresos':
 * @property string $idEgreso
 * @property string $idConcepto
 * @property string $Detalle
 * @property string $Fecha
 * @property string $Monto
 *
 * The followings are the available model relations:
 * @property Conceptos $idConcepto0
 */
class Egresos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Egresos the static model class
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
		return 'egresos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idConcepto', 'required'),
			array('idConcepto', 'length', 'max'=>10),
			array('Detalle', 'length', 'max'=>250),
			array('Monto', 'length', 'max'=>12),
			array('Fecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idEgreso, idConcepto, Detalle, Fecha, Monto', 'safe', 'on'=>'search'),
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
			'Conceptos' => array(self::BELONGS_TO, 'Conceptos', 'idConcepto'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idEgreso' => 'Id Egreso',
			'idConcepto' => 'Id Concepto',
			'Detalle' => 'Detalle',
			'Fecha' => 'Fecha',
			'Monto' => 'Monto',
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

		$criteria->compare('idEgreso',$this->idEgreso,true);
		$criteria->compare('idConcepto',$this->idConcepto,true);
		$criteria->compare('Detalle',$this->Detalle,true);
		$criteria->compare('Fecha',$this->Fecha,true);
		$criteria->compare('Monto',$this->Monto,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function EgresosFecha($DesdeFecha, $HastaFecha){
		$criteria=new CDbCriteria;
		$criteria->select = "idConcepto, sum(Monto) as Monto";
		$criteria->condition = "Fecha between :DesdeFecha and :HastaFecha";
		$criteria->params = array("DesdeFecha"=>$DesdeFecha, "HastaFecha"=>$HastaFecha);
		$criteria->group = "idConcepto";
		
		return Egresos::model()->findAll($criteria); 
		
	}

	public function MovimientoConcepto($idConcepto){
		$criteria=new CDbCriteria;
		$criteria->condition = "idConcepto = :idConcepto";
		$criteria->params 	= array('idConcepto'=>$idConcepto);
		$criteria->order	= "fecha";
		
		return Egresos::model()->findAll($criteria);
	}	

}