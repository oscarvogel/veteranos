<?php

/**
 * This is the model class for table "torneo".
 *
 * The followings are the available columns in table 'torneo':
 * @property string $idTorneo
 * @property string $Nombre
 * @property string $Inicio
 * @property string $Estado
 */
class Torneo extends CActiveRecord
{
	public static $Estado = array(
			'A'=>'Activo',
			'B'=>'Baja',
			'S'=>'Suspendido',
			'I'=>'Iniciado',
			'F'=>'Finalizado',
			);

	public $DesdeFecha;
	public $HastaFecha;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Torneo the static model class
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
		return 'torneo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre, Estado', 'required'),
			array('Nombre', 'length', 'max'=>50),
			array('Estado', 'length', 'max'=>1),
			array('Inicio, InicioTorneo', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idTorneo, Nombre, Inicio, Estado, InicioTorneo', 'safe', 'on'=>'search'),
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
            'equipostorneos' => array(self::HAS_MANY, 'Equipostorneo', 'idTorneo'),
            'fixtures' => array(self::HAS_MANY, 'Fixture', 'idTorneo'),
            'historicojugador' => array(self::HAS_MANY, 'Historicojugador', 'idTorneo'),
            'posicionestorneo' => array(self::HAS_MANY, 'Posicionestorneo', 'idTorneo'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idTorneo' => 'Id Torneo',
			'Nombre' => 'Nombre',
			'Inicio' => 'Inicio',
			'Estado' => 'Estado',
			'InicioTorneo' => 'Torneo Iniciado',
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

		$criteria->compare('idTorneo',$this->idTorneo,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Inicio',$this->Inicio,true);
		$criteria->compare('Estado',$this->Estado,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getEstado($key=null)
	{
		if($key!==null)
			return self::$Estado[$key];
		return self::$Estado;

	}
	
	public static function getListTorneo($estado = ''){
		$criteria = new CDbCriteria;
		if($estado != ''){
			$criteria->condition = "estado in('" . $estado . "')";
			//$criteria->params = array('estado'=>$estado);
			//echo $estado;
		}
		return CHtml::listData(Torneo::model()->findAll($criteria),'idTorneo','Nombre');
	}
	
	public static function buscarPorClave($id){
		$torneo = Torneo::model()->findByPk($id);
		return $torneo;
	}
}