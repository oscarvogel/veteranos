<?php

/**
 * This is the model class for table "equipos".
 *
 * The followings are the available columns in table 'equipos':
 * @property string $idEquipo
 * @property string $Nombre
 * @property string $Delegado
 * @property string $idCategoria
 *
 * The followings are the available model relations:
 * @property Categorias $idCategoria0
 * @property Equipostoreno[] $equipostorenos
 */
class Equipos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Equipos the static model class
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
		return 'equipos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Nombre, Delegado', 'required'),
            array('idUsuario', 'numerical', 'integerOnly'=>true),
			array('Nombre, Delegado, DelegadoSuplente, Camiseta, CamisetaSuplente', 'length', 'max'=>50),
			array('idCategoria, Cancha', 'length', 'max'=>10),
			array('Telefono', 'length', 'max'=>100),
			array('Correo', 'length', 'max'=>255),
			array('Correo', 'email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idEquipo, Nombre, Delegado, DelegadoSuplente, Camiseta, CamisetaSuplente, Cancha, idCategoria, Correo, Telefono, idEquipo', 'safe', 'on'=>'search'),
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
			'cancha' => array(self::BELONGS_TO, 'Canchas', 'Cancha'),
			'Categoria' => array(self::BELONGS_TO, 'Categorias', 'idCategoria'),
			'Equipostorneo' => array(self::HAS_MANY, 'Equipostorneo', 'idEquipo'),
			'Local' => array(self::HAS_MANY, 'Fixture', 'Local'),
            'Visitante' => array(self::HAS_MANY, 'Fixture', 'Visitante'),
            'Jugador' => array(self::HAS_MANY, 'Jugador', 'idEquipo'),
            'Usuario' => array(self::BELONGS_TO,'CrugeUser', 'idUsuario'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idEquipo' => 'Id Equipo',
			'Nombre' => 'Nombre',
			'Delegado' => 'Delegado',
			'idCategoria' => 'Id Categoria',
			'DelegadoSuplente' => 'Delegado Suplente',
            'Camiseta' => 'Camiseta',
            'CamisetaSuplente' => 'Camiseta Suplente',
            'Cancha' => 'Cancha',
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

		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Delegado',$this->Delegado,true);
		$criteria->compare('idCategoria',$this->idCategoria,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	static public function getListEquipo(){
		$criteria = new CDbCriteria;
		$criteria->order = "LOWER(TRIM(Nombre)) ASC";
		return CHtml::listData(Equipos::model()->findAll($criteria),'idEquipo','Nombre');
	}


	public static function EquiposTorneo($idTorneo){
		$criteria=new CDbCriteria;
		$criteria->join = "inner join equipostorneo on t.idEquipo = equipostorneo.idEquipo";
		$criteria->condition = "equipostorneo.idTorneo = " . $idTorneo; 
		$criteria->order = "t.Nombre";
		foreach(Equipos::model()->findAll($criteria) as $e){
			$equipos[] = $e->idEquipo . '-' . $e->Nombre;
		}
		return $equipos;
	}
	
	public static function equiposAutoComplete($name='') {
       // Recommended: Secure Way to Write SQL in Yii 
    	$name = "%" . $name;
		$sql= "SELECT Nombre AS label, idEquipo as id 
					FROM equipos 
					WHERE nombre LIKE :name
					ORDER BY nombre
					LIMIT 0,50";
        $name = $name."%";
        return Yii::app()->db->createCommand($sql)->queryAll(true,array(':name'=>$name));
	}

}
