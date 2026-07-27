<?php

class CuotaSocialPago extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'cuota_social_pago';
	}

	public function rules()
	{
		return array(
			array('idJugador, periodo, fecha_pago', 'required'),
			array('idJugador, idUsuario', 'numerical', 'integerOnly'=>true),
			array('periodo', 'length', 'is'=>7),
			array('periodo', 'validarPeriodo'),
			array('observacion', 'length', 'max'=>250),
			array('created_at, updated_at', 'safe'),
			array('idPago, idJugador, periodo, fecha_pago, idUsuario, observacion', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'Jugador' => array(self::BELONGS_TO, 'Jugador', 'idJugador'),
			'Usuario' => array(self::BELONGS_TO, 'CrugeStoredUser', 'idUsuario'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'idPago' => 'Id Pago',
			'idJugador' => 'Jugador',
			'periodo' => 'Periodo',
			'fecha_pago' => 'Fecha de Pago',
			'idUsuario' => 'Usuario',
			'observacion' => 'Observacion',
			'created_at' => 'Creado',
			'updated_at' => 'Actualizado',
		);
	}

	protected function beforeValidate()
	{
		$this->periodo = self::normalizarPeriodo($this->periodo);
		return parent::beforeValidate();
	}

	protected function beforeSave()
	{
		if(parent::beforeSave()) {
			$now = date('Y-m-d H:i:s');
			if($this->isNewRecord)
				$this->created_at = $now;
			$this->updated_at = $now;
			return true;
		}
		return false;
	}

	public function validarPeriodo($attribute, $params)
	{
		if(!self::esPeriodoValido($this->$attribute))
			$this->addError($attribute, 'El periodo debe tener formato aaaa-mm.');
	}

	public static function normalizarPeriodo($periodo)
	{
		$periodo = trim((string)$periodo);
		if($periodo === '')
			return date('Y-m');

		if(preg_match('/^(\d{4})-(\d{1,2})$/', $periodo, $matches))
			return sprintf('%04d-%02d', (int)$matches[1], (int)$matches[2]);

		return $periodo;
	}

	public static function esPeriodoValido($periodo)
	{
		if(preg_match('/^(\d{4})-(\d{2})$/', $periodo, $matches) !== 1)
			return false;

		$mes = (int)$matches[2];
		return (int)$matches[1] >= 2000 && (int)$matches[1] <= 2100 && $mes >= 1 && $mes <= 12;
	}

	public static function marcarPagosPeriodo($idEquipo, $periodo, $socioIds, $pagadoIds, $idUsuario = null)
	{
		$idEquipo = (int)$idEquipo;
		$periodo = self::normalizarPeriodo($periodo);
		if($idEquipo <= 0 || !self::esPeriodoValido($periodo))
			throw new CException('Equipo o periodo invalido.');

		$socioIds = self::normalizarIds($socioIds);
		$pagadoIds = self::normalizarIds($pagadoIds);
		$jugadores = self::getJugadoresEquipo($idEquipo);
		$jugadorIds = array();
		foreach($jugadores as $jugador)
			$jugadorIds[] = (int)$jugador->idJugador;

		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach($jugadores as $jugador) {
				$esSocio = in_array((int)$jugador->idJugador, $socioIds, true) ? 1 : 0;
				if((int)$jugador->es_socio !== $esSocio) {
					$jugador->es_socio = $esSocio;
					if(!$jugador->save(false, array('es_socio')))
						throw new CException('No se pudo actualizar la condicion de socio.');
				}
			}

			if(!empty($jugadorIds)) {
				$criteria = new CDbCriteria;
				$criteria->addInCondition('idJugador', $jugadorIds);
				$criteria->compare('periodo', $periodo);
				$pagosActuales = self::model()->findAll($criteria);
				foreach($pagosActuales as $pago) {
					if(!in_array((int)$pago->idJugador, $pagadoIds, true))
						$pago->delete();
				}
			}

			foreach($pagadoIds as $idJugador) {
				if(!in_array($idJugador, $jugadorIds, true))
					continue;

				$pago = self::model()->findByAttributes(array('idJugador'=>$idJugador, 'periodo'=>$periodo));
				if($pago === null) {
					$pago = new self;
					$pago->idJugador = $idJugador;
					$pago->periodo = $periodo;
					$pago->fecha_pago = date('Y-m-d');
					$pago->idUsuario = $idUsuario;
					if(!$pago->save())
						throw new CException('No se pudo guardar el pago de cuota.');
				}
			}

			$transaction->commit();
		} catch(Exception $e) {
			if($transaction->active)
				$transaction->rollback();
			throw $e;
		}

		return self::contarEstadoEquipo($idEquipo, $periodo);
	}

	public static function getEstadoEquipo($idEquipo, $periodo)
	{
		$idEquipo = (int)$idEquipo;
		$periodo = self::normalizarPeriodo($periodo);
		if($idEquipo <= 0 || !self::esPeriodoValido($periodo))
			throw new CException('Equipo o periodo invalido.');

		$equipo = Equipos::model()->findByPk($idEquipo);
		if($equipo === null)
			throw new CHttpException(404, 'El equipo solicitado no existe.');

		$pagos = self::getPagosPorJugador($idEquipo, $periodo);
		$jugadores = array();
		foreach(self::getJugadoresEquipo($idEquipo) as $jugador) {
			$esSocio = (int)$jugador->es_socio === 1;
			$pagado = isset($pagos[(int)$jugador->idJugador]);
			$jugadores[] = array(
				'idJugador'=>(int)$jugador->idJugador,
				'Nombre'=>$jugador->Nombre,
				'esSocio'=>$esSocio,
				'pagado'=>$pagado,
				'estado'=>$esSocio ? ($pagado ? 'Pagado' : 'Pendiente') : 'No socio',
			);
		}

		return array(
			'equipo'=>array(
				'idEquipo'=>(int)$equipo->idEquipo,
				'Nombre'=>$equipo->Nombre,
			),
			'periodo'=>$periodo,
			'jugadores'=>$jugadores,
			'totales'=>self::contarEstadoEquipo($idEquipo, $periodo),
		);
	}

	public static function getInformePeriodo($periodo, $idEquipo = '', $estado = '')
	{
		$periodo = self::normalizarPeriodo($periodo);
		if(!self::esPeriodoValido($periodo))
			throw new CException('Periodo invalido.');

		$criteria = new CDbCriteria;
		if($idEquipo !== '')
			$criteria->compare('idEquipo', (int)$idEquipo);
		$criteria->order = 'Nombre ASC';
		$jugadores = Jugador::model()->findAll($criteria);

		$pagos = self::getPagosPorJugador($idEquipo, $periodo);
		$informe = array(
			'periodo'=>$periodo,
			'idEquipo'=>$idEquipo,
			'estado'=>$estado,
			'alDia'=>array(),
			'pendientes'=>array(),
			'noSocios'=>array(),
		);

		foreach($jugadores as $jugador) {
			$row = array(
				'idJugador'=>(int)$jugador->idJugador,
				'Nombre'=>$jugador->Nombre,
				'Equipo'=>$jugador->Equipo ? $jugador->Equipo->Nombre : '',
				'idEquipo'=>(int)$jugador->idEquipo,
				'esSocio'=>(int)$jugador->es_socio === 1,
				'pagado'=>isset($pagos[(int)$jugador->idJugador]),
			);
			if(!$row['esSocio']) {
				$row['estado'] = 'No socio';
				if($estado === '' || $estado === 'noSocio')
					$informe['noSocios'][] = $row;
			} elseif($row['pagado']) {
				$row['estado'] = 'Al dia';
				if($estado === '' || $estado === 'alDia')
					$informe['alDia'][] = $row;
			} else {
				$row['estado'] = 'Pendiente';
				if($estado === '' || $estado === 'pendiente')
					$informe['pendientes'][] = $row;
			}
		}

		return $informe;
	}

	public static function getPagosPorJugador($idEquipo, $periodo)
	{
		$command = Yii::app()->db->createCommand()
			->select('p.idJugador, p.idPago')
			->from('cuota_social_pago p')
			->join('jugador j', 'j.idJugador = p.idJugador')
			->where('p.periodo = :periodo', array(':periodo'=>$periodo));

		if($idEquipo !== '' && $idEquipo !== null)
			$command->andWhere('j.idEquipo = :idEquipo', array(':idEquipo'=>(int)$idEquipo));

		$rows = $command->queryAll();
		$pagos = array();
		foreach($rows as $row)
			$pagos[(int)$row['idJugador']] = (int)$row['idPago'];
		return $pagos;
	}

	private static function contarEstadoEquipo($idEquipo, $periodo)
	{
		$estado = self::getEstadoEquipoSinTotales($idEquipo, $periodo);
		$totales = array('socios'=>0, 'alDia'=>0, 'pendientes'=>0, 'noSocios'=>0, 'pagados'=>0);
		foreach($estado as $row) {
			if($row['esSocio']) {
				$totales['socios']++;
				if($row['pagado']) {
					$totales['alDia']++;
					$totales['pagados']++;
				} else {
					$totales['pendientes']++;
				}
			} else {
				$totales['noSocios']++;
			}
		}
		return $totales;
	}

	private static function getEstadoEquipoSinTotales($idEquipo, $periodo)
	{
		$pagos = self::getPagosPorJugador($idEquipo, $periodo);
		$rows = array();
		foreach(self::getJugadoresEquipo($idEquipo) as $jugador) {
			$rows[] = array(
				'esSocio'=>(int)$jugador->es_socio === 1,
				'pagado'=>isset($pagos[(int)$jugador->idJugador]),
			);
		}
		return $rows;
	}

	private static function getJugadoresEquipo($idEquipo)
	{
		return Jugador::model()->findAllByAttributes(
			array('idEquipo'=>(int)$idEquipo),
			array('order'=>'Nombre ASC')
		);
	}

	private static function normalizarIds($ids)
	{
		if(!is_array($ids))
			return array();

		$result = array();
		foreach($ids as $id) {
			$id = (int)$id;
			if($id > 0 && !in_array($id, $result, true))
				$result[] = $id;
		}
		return $result;
	}
}
