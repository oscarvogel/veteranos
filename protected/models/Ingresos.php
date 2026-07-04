<?php

/**
 * This is the model class for table "ingresos".
 *
 * The followings are the available columns in table 'ingresos':
 * @property string $idIngreso
 * @property string $idEquipo
 * @property integer $NFecha
 * @property string $Fecha
 * @property string $Hora
 * @property string $Monto
 * @property string $idConcepto
 * @property integer $NumeroRecibo
 * @property string $Estado
 * @property integer $idUsuario
 * @property string $FechaAlta
 * @property string $FechaAnulacion
 * @property string $MotivoAnulacion
 * @property string $ReciboToken
 *
 * The followings are the available model relations:
 * @property Conceptos $idConcepto0
 * @property Equipos $idEquipo0
 */
class Ingresos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Ingresos the static model class
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
		return 'ingresos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idEquipo, idConcepto, Fecha, Monto', 'required'),
			array('NFecha, NumeroRecibo, idUsuario', 'numerical', 'integerOnly'=>true),
			array('Monto', 'numerical', 'min'=>0.01),
			array('Estado', 'in', 'range'=>array('VIGENTE','ANULADO')),
			array('idEquipo, idConcepto', 'length', 'max'=>10),
			array('Detalle, MotivoAnulacion', 'length', 'max'=>250),
			array('Fecha, Hora, FechaAlta, FechaAnulacion, ReciboToken', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idIngreso, idEquipo, NFecha, Fecha, Hora, Monto, idConcepto, Detalle, NumeroRecibo, Estado, idUsuario', 'safe', 'on'=>'search'),
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
			'Equipos' => array(self::BELONGS_TO, 'Equipos', 'idEquipo'),
			'Usuario' => array(self::BELONGS_TO, 'CrugeStoredUser', 'idUsuario'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idIngreso' => 'Id Ingreso',
			'idEquipo' => 'Equipo',
			'NFecha' => 'Nº de Fecha',
			'Fecha' => 'Fecha',
			'Hora' => 'Hora',
			'Monto' => 'Monto',
			'idConcepto' => 'Tipo de cobro',
			'Detalle' => 'Detalle',
			'NumeroRecibo' => 'Nro. Recibo',
			'Estado' => 'Estado',
			'idUsuario' => 'Cobrador',
			'FechaAlta' => 'Fecha de Alta',
			'FechaAnulacion' => 'Fecha de Anulacion',
			'MotivoAnulacion' => 'Motivo de Anulacion',
			'ReciboToken' => 'Token publico',
		);
	}

	protected function beforeSave()
	{
		if(parent::beforeSave()) {
			if($this->ReciboToken === null || $this->ReciboToken === '') {
				$this->ReciboToken = self::generarReciboToken();
			}
			return true;
		}
		return false;
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

		$criteria->compare('idIngreso',$this->idIngreso,true);
		$criteria->compare('idEquipo',$this->idEquipo,true);
		$criteria->compare('NFecha',$this->NFecha);
		$criteria->compare('Fecha',$this->Fecha,true);
		$criteria->compare('Hora',$this->Hora,true);
		$criteria->compare('Monto',$this->Monto,true);
		$criteria->compare('idConcepto',$this->idConcepto,true);
		$criteria->compare('NumeroRecibo',$this->NumeroRecibo);
		$criteria->compare('Estado',$this->Estado,true);
		$criteria->compare('idUsuario',$this->idUsuario);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.NumeroRecibo DESC, t.idIngreso DESC',
			),
		));
	}

	public static function siguienteNumeroRecibo()
	{
		$max = Yii::app()->db->createCommand()
			->select('MAX(NumeroRecibo)')
			->from('ingresos')
			->queryScalar();

		return ((int)$max) + 1;
	}

	public static function generarReciboToken()
	{
		do {
			if(function_exists('openssl_random_pseudo_bytes')) {
				$token = bin2hex(openssl_random_pseudo_bytes(32));
			} else {
				$token = hash('sha256', uniqid('', true) . mt_rand());
			}
			$exists = (int)Yii::app()->db->createCommand()
				->select('COUNT(*)')
				->from('ingresos')
				->where('ReciboToken = :token', array(':token'=>$token))
				->queryScalar();
		} while($exists > 0);

		return $token;
	}

	public static function findByReciboToken($token)
	{
		if(!is_string($token) || preg_match('/^[a-f0-9]{64}$/', $token) !== 1)
			return null;

		return self::model()->findByAttributes(array('ReciboToken'=>$token));
	}

	public function ensureReciboToken()
	{
		if($this->ReciboToken !== null && $this->ReciboToken !== '')
			return true;

		$this->ReciboToken = self::generarReciboToken();
		return $this->save(false, array('ReciboToken'));
	}

	public function getReciboPublicoUrl()
	{
		$this->ensureReciboToken();
		$hostInfo = 'http://veteranos.ar';
		if(Yii::app()->hasComponent('request')) {
			$hostInfo = Yii::app()->request->hostInfo;
		}

		return rtrim($hostInfo, '/') . '/index.php?r=ingresos/reciboPublico&token=' . rawurlencode($this->ReciboToken);
	}

	public function getWhatsappUrl()
	{
		$numero = $this->NumeroRecibo ? $this->NumeroRecibo : $this->idIngreso;
		$equipo = $this->Equipos ? $this->Equipos->Nombre : '';
		$concepto = $this->Conceptos ? $this->Conceptos->Nombre : '';
		$mensaje = 'Recibo Nro. ' . str_pad($numero, 8, '0', STR_PAD_LEFT) .
			' - ' . $equipo .
			' - ' . $concepto .
			' - $ ' . number_format((float)$this->Monto, 2, ',', '.') .
			'. Descargar PDF: ' . $this->getReciboPublicoUrl();

		return 'https://wa.me/?text=' . rawurlencode($mensaje);
	}

	public static function getArqueoCaja($desdeFecha, $hastaFecha, $idUsuario = null)
	{
		$criteria = new CDbCriteria;
		$criteria->with = array('Equipos', 'Conceptos', 'Usuario');
		$criteria->condition = 't.Fecha BETWEEN :desde AND :hasta';
		$criteria->params = array(':desde'=>$desdeFecha, ':hasta'=>$hastaFecha);
		if($idUsuario !== null && $idUsuario !== '') {
			$criteria->addCondition('t.idUsuario = :idUsuario');
			$criteria->params[':idUsuario'] = $idUsuario;
		}
		$criteria->order = 't.Fecha ASC, t.NumeroRecibo ASC';

		return new CActiveDataProvider('Ingresos', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>100),
		));
	}

	public static function getTotalArqueoCaja($desdeFecha, $hastaFecha, $idUsuario = null)
	{
		$command = Yii::app()->db->createCommand()
			->select('COALESCE(SUM(Monto), 0)')
			->from('ingresos')
			->where('Fecha BETWEEN :desde AND :hasta AND Estado = :estado', array(
				':desde'=>$desdeFecha,
				':hasta'=>$hastaFecha,
				':estado'=>'VIGENTE',
			));

		if($idUsuario !== null && $idUsuario !== '') {
			$command->andWhere('idUsuario = :idUsuario', array(':idUsuario'=>$idUsuario));
		}

		return $command->queryScalar();
	}

	public static function getResumenMensual($anio, $mes)
	{
		$anio = (int)$anio;
		$mes = (int)$mes;
		if($anio < 2000 || $anio > 2100)
			$anio = (int)date('Y');
		if($mes < 1 || $mes > 12)
			$mes = (int)date('n');

		$desde = sprintf('%04d-%02d-01', $anio, $mes);
		$hasta = date('Y-m-t', strtotime($desde));

		$rows = Yii::app()->db->createCommand()
			->select("c.idConcepto, c.Nombre,
				COUNT(i.idIngreso) AS cantidadRecibos,
				SUM(CASE WHEN i.Estado = 'VIGENTE' THEN 1 ELSE 0 END) AS cantidadVigente,
				SUM(CASE WHEN i.Estado = 'ANULADO' THEN 1 ELSE 0 END) AS cantidadAnulada,
				COALESCE(SUM(CASE WHEN i.Estado = 'VIGENTE' THEN i.Monto ELSE 0 END), 0) AS totalVigente,
				COALESCE(SUM(CASE WHEN i.Estado = 'ANULADO' THEN i.Monto ELSE 0 END), 0) AS totalAnulado")
			->from('ingresos i')
			->join('conceptos c', 'c.idConcepto = i.idConcepto')
			->where('i.Fecha BETWEEN :desde AND :hasta', array(':desde'=>$desde, ':hasta'=>$hasta))
			->group('c.idConcepto, c.Nombre')
			->order('totalVigente DESC, c.Nombre ASC')
			->queryAll();

		$totalVigente = 0;
		$totalAnulado = 0;
		$cantidadRecibos = 0;
		$cantidadVigente = 0;
		$cantidadAnulada = 0;
		foreach($rows as $index=>$row) {
			$rows[$index]['cantidadRecibos'] = (int)$row['cantidadRecibos'];
			$rows[$index]['cantidadVigente'] = (int)$row['cantidadVigente'];
			$rows[$index]['cantidadAnulada'] = (int)$row['cantidadAnulada'];
			$rows[$index]['totalVigente'] = (float)$row['totalVigente'];
			$rows[$index]['totalAnulado'] = (float)$row['totalAnulado'];
			$totalVigente += (float)$row['totalVigente'];
			$totalAnulado += (float)$row['totalAnulado'];
			$cantidadRecibos += (int)$row['cantidadRecibos'];
			$cantidadVigente += (int)$row['cantidadVigente'];
			$cantidadAnulada += (int)$row['cantidadAnulada'];
		}

		$porDia = array();
		$diasDelMes = (int)date('t', strtotime($desde));
		for($dia = 1; $dia <= $diasDelMes; $dia++) {
			$fecha = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
			$porDia[$fecha] = 0.0;
		}

		$dailyRows = Yii::app()->db->createCommand()
			->select("Fecha, COALESCE(SUM(Monto), 0) AS total")
			->from('ingresos')
			->where('Fecha BETWEEN :desde AND :hasta AND Estado = :estado', array(
				':desde'=>$desde,
				':hasta'=>$hasta,
				':estado'=>'VIGENTE',
			))
			->group('Fecha')
			->order('Fecha ASC')
			->queryAll();
		foreach($dailyRows as $row) {
			$porDia[$row['Fecha']] = (float)$row['total'];
		}

		return array(
			'desde'=>$desde,
			'hasta'=>$hasta,
			'anio'=>$anio,
			'mes'=>$mes,
			'kpis'=>array(
				'totalVigente'=>$totalVigente,
				'totalAnulado'=>$totalAnulado,
				'cantidadRecibos'=>$cantidadRecibos,
				'cantidadVigente'=>$cantidadVigente,
				'cantidadAnulada'=>$cantidadAnulada,
				'promedioVigente'=>$cantidadVigente > 0 ? $totalVigente / $cantidadVigente : 0,
			),
			'porConcepto'=>$rows,
			'porDia'=>$porDia,
		);
	}

	public function anular($motivo)
	{
		if($this->Estado === 'ANULADO') {
			$this->addError('Estado', 'El recibo ya esta anulado.');
			return false;
		}

		$this->Estado = 'ANULADO';
		$this->MotivoAnulacion = $motivo;
		$this->FechaAnulacion = date('Y-m-d H:i:s');

		return $this->save(false, array('Estado', 'MotivoAnulacion', 'FechaAnulacion'));
	}


	public function IngresoEquipo($idEquipo){
		$criteria=new CDbCriteria;
		$criteria->condition = "idEquipo = " . $idEquipo;
		
		return Ingresos::model()->findAll($criteria); 
	}
	
	
	public function IngresosFecha($DesdeFecha, $HastaFecha){
		$criteria=new CDbCriteria;
		$criteria->select = "idConcepto, sum(Monto) as Monto";
		$criteria->condition = "Fecha between :DesdeFecha and :HastaFecha";
		$criteria->params = array("DesdeFecha"=>$DesdeFecha, "HastaFecha"=>$HastaFecha);
		$criteria->group = "idConcepto";
		
		//print_r($criteria);
		//Yii::app()->end();
		return Ingresos::model()->findAll($criteria); 
		
	}
	
	
	public function MovimientoConcepto($idConcepto){
		$criteria=new CDbCriteria;
		$criteria->condition = "idConcepto = :idConcepto";
		$criteria->params 	= array('idConcepto'=>$idConcepto);
		$criteria->order	= "fecha";
		
		return Ingresos::model()->findAll($criteria);
	}	


	public function getPagosFecha($fecha){

		$criteria=new CDbCriteria;
		$criteria->condition = "Fecha = :Fecha";
		$criteria->params 	= array('Fecha'=>$fecha);
		$criteria->join 	= " inner join equipos on t.idEquipo = equipos.idEquipo";
		$criteria->order	= 'equipos.nombre';
		
		$dataProvider = new CActiveDataProvider('Ingresos', array(
			'criteria'=>$criteria, 'pagination'=>array('pageSize'=>50,),
		));
		
		return $dataProvider;
	}

	public function getIngresosTipo($idEquipo, $idConcepto){
		$criteria=new CDbCriteria;
		$criteria->condition 	= "t.idEquipo = :idEquipo and t.idConcepto = :idConcepto";
		$criteria->params		= array('idEquipo'=>$idEquipo, 'idConcepto'=>$idConcepto);
		$criteria->join 		= " inner join equipos on t.idEquipo = equipos.idEquipo";
		$criteria->order		= 't.fecha desc';
		
		$dataProvider = new CActiveDataProvider('Ingresos', array(
			'criteria'=>$criteria, 'pagination'=>array('pageSize'=>50,),
		));
		
		return $dataProvider;

	}
}
