<?php

/**
 * This is the model class for table "jugador".
 *
 * The followings are the available columns in table 'jugador':
 * @property string $idJugador
 * @property string $Nombre
 * @property string $Clase
 * @property string $DNI
 * @property string $idEquipo
 * @property string $fecha_nacimiento
 *
 * The followings are the available model relations:
 * @property Equipos $idEquipo0
 */
class Jugador extends CActiveRecord
{
	public $Busqueda;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Jugador the static model class
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
		return 'jugador';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idEquipo', 'required'),
			array('Nombre', 'length', 'max'=>200),
			array('Clase', 'length', 'max'=>4),
			array('DNI', 'length', 'max'=>8),
			array('DNI', 'unique'),
			array('idEquipo', 'length', 'max'=>10),
			array('Observacion', 'length', 'max'=>200),
			array('certificado, firma_lista, fotocopia_dni, dec_jurada', 'safe'),
			array('fecha_nacimiento', 'required'),
			array('fecha_nacimiento', 'validarFechaNacimiento'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idJugador, Nombre, Clase, DNI, idEquipo, Observacion, certificado, firma_lista, fotocopia_dni, dec_jurada, fecha_nacimiento', 'safe', 'on'=>'search'),
		);
	}

	protected function beforeValidate()
	{
		$this->normalizarFechaNacimiento();
		return parent::beforeValidate();
	}

	private function normalizarFechaNacimiento()
	{
		$fecha = trim((string)$this->fecha_nacimiento);
		if($fecha === '')
			return;

		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $matches))
		{
			$ano = $matches[1];
			$mes = $matches[2];
			$dia = $matches[3];
		}
		elseif(preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $matches))
		{
			$dia = $matches[1];
			$mes = $matches[2];
			$ano = $matches[3];
		}
		elseif(preg_match('/^(\d{2})(\d{2})(\d{4})$/', $fecha, $matches))
		{
			$dia = $matches[1];
			$mes = $matches[2];
			$ano = $matches[3];
		}
		else
			return;

		if(checkdate((int)$mes, (int)$dia, (int)$ano))
		{
			$this->fecha_nacimiento = $ano . '-' . $mes . '-' . $dia;
			$this->Clase = $ano;
		}
	}

	public function validarFechaNacimiento($attribute,$params)
	{
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $this->$attribute, $matches)
			|| !checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]))
		{
			$this->addError($attribute, 'La fecha de nacimiento debe tener el formato dd/mm/aaaa.');
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'Equipo' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
			'DocumentosLegajo' => array(self::HAS_MANY, 'JugadorDocumento', 'idJugador', 'order'=>'DocumentosLegajo.created_at DESC'),
		);
	}

	public function getCantidadDocumentosLegajo()
	{
		return (int)JugadorDocumento::model()->countByAttributes(array('idJugador'=>$this->idJugador));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idJugador' => 'Id Jugador',
			'Nombre' => 'Nombre',
			'Clase' => 'Clase',
			'DNI' => 'Dni',
			'idEquipo' => 'Id Equipo',
			'Observacion'=>'Ovservaciones',
			'certificado'=>'Certificado Buena salud',
			'firma_lista'=>'Lista Firmada',
			'fotocopia_dni'=>'Fotocopia DNI',
			'dec_jurada'=>'Declaracion Jurada',
			'fecha_nacimiento'=>'Fecha de Nacimiento',
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

		$criteria->compare('idJugador',$this->idJugador,true);
		$criteria->compare('Nombre',$this->Nombre,true);
		$criteria->compare('Clase',$this->Clase,true);
		$criteria->compare('DNI',$this->DNI,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('certificado',$this->certificado,true);
		$criteria->compare('firma_lista',$this->firma_lista,true);
		$criteria->compare('fotocopia_dni',$this->fotocopia_dni,true);
		$criteria->compare('fecha_nacimiento',$this->fecha_nacimiento,true);
		$criteria->compare('dec_jurada',$this->dec_jurada,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getListJugador($idEquipo1, $idEquipo2, $torneo = ''){
		if(isset($idEquipo1) && isset($idEquipo2)){
			$criteria=new CDbCriteria;
			$criteria->order = 'Nombre';
			if($torneo != ''){
				if($torneo->Estado == 'F'){
					$criteria->condition = 'historicojugador.idEquipo in(' . $idEquipo1 . ',' . $idEquipo2 . ')';
					$criteria->join = "inner join historicojugador on t.idJugador = historicojugador.idJugador";
				}else{
					$criteria->condition = 'idEquipo in(' . $idEquipo1 . ',' . $idEquipo2 . ')';				
				}
			}else{
				$criteria->condition = 'idEquipo in(' . $idEquipo1 . ',' . $idEquipo2 . ')';
			}
			
			return CHtml::listData(Jugador::model()->findAll($criteria),'idJugador','Nombre');
		}
		return CHtml::listData(Jugador::model()->findAll(array('order'=>'Nombre')),'idJugador','Nombre');
	}


	public static function ListaBuenaFe($idTorneo, $idEquipo, $torneo){
		$criteria=new CDbCriteria;
		if($torneo->Estado == 'F'){
			$criteria->condition = "historicojugador.idTorneo =:idTorneo and historicojugador.idEquipo =:idEquipo";
			$criteria->join = "inner join historicojugador on t.idJugador = historicojugador.idJugador";
			$criteria->params = array('idTorneo'=>$torneo->idTorneo, 'idEquipo'=>$idEquipo);	
		}else{
			$criteria->condition = "equipostorneo.idTorneo = " . $idTorneo . " and t.idEquipo = " . $idEquipo;
			$criteria->join = "inner join equipostorneo on t.idEquipo=equipostorneo.idEquipo";
		}
		$criteria->order = "t.Nombre";
		return Jugador::model()->findAll($criteria); 
	}
	
	public static function jugadorAutoComplete($name='') {
       // Recommended: Secure Way to Write SQL in Yii 
    	$name = "%" . $name;
		$sql= "SELECT jugador.Nombre AS value,
					CONCAT(jugador.Nombre, ' (', COALESCE(equipos.Nombre, 'Sin equipo'), ')') AS label,
					jugador.idJugador AS id
					FROM jugador
					LEFT JOIN equipos ON jugador.idEquipo = equipos.idEquipo
					WHERE jugador.Nombre LIKE :name
					ORDER BY jugador.Nombre
					LIMIT 0,30";
        $name = $name."%";
        return Yii::app()->db->createCommand($sql)->queryAll(true,array(':name'=>$name));
	}
	
	
	public function Liberar($idTorneo){
		$sql = "insert into historicojugador (idEquipo, idtorneo, idjugador)
				select jugador.idEquipo, equipostorneo.idTorneo, jugador.idJugador
					from jugador
						inner join equipostorneo
						on jugador.idEquipo = equipostorneo.idEquipo
					where equipostorneo.idTorneo = " . $idTorneo;
		$result = Yii::app()->db->createCommand($sql)->execute();
		$sql = "update jugador set idEquipo = 0 
					where idEquipo in (select idEquipo from equipostorneo where equipostorneo.idTorneo = " . $idTorneo . ")";
		$result = Yii::app()->db->createCommand($sql)->execute();			
	}
	
	public static function ConsultaEquiposJugador($idJugador){
		$criteria=new CDbCriteria;
		$criteria->join = "inner join historicojugador hj on t.idJugador = hj.idJugador inner join torneo on hj.idTorneo = torneo.idTorneo inner join equipos on hj.idEquipo = equipos.idEquipo";
		$criteria->condition = "hj.idJugador=:idJugador";
		$criteria->params = array('idJugador'=>$idJugador);
		
		return Jugador::model()->findAll($criteria);
	}	
}
