<?php

class SociosCuotaController extends Controller
{
	public $layout='//layouts/column2';

	public function filters()
	{
		return array();
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('estadoEquipo'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('equipo','guardar','informe'),
				'users'=>array('admin','oscarvogel','teo'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	protected function beforeAction($action)
	{
		if($action->id === 'estadoEquipo')
			return parent::beforeAction($action);

		if(Yii::app()->user->isGuest || !$this->puedeGestionarCuotas())
			throw new CHttpException(401, 'No tiene permiso para gestionar cuotas sociales.');

		return parent::beforeAction($action);
	}

	private function puedeGestionarCuotas()
	{
		foreach(array('action_sociosCuota_equipo', 'action_sociosCuota_informe', 'action_ingresos_create', 'action_ingresos_admin') as $operation) {
			if(Yii::app()->user->checkAccess($operation))
				return true;
		}

		return in_array(Yii::app()->user->name, array('admin', 'oscarvogel', 'teo'), true);
	}

	public function actionEquipo()
	{
		$idEquipo = isset($_GET['idEquipo']) ? (int)$_GET['idEquipo'] : 0;
		$periodo = CuotaSocialPago::normalizarPeriodo(isset($_GET['periodo']) ? $_GET['periodo'] : date('Y-m'));
		$estado = null;

		if($idEquipo > 0)
			$estado = CuotaSocialPago::getEstadoEquipo($idEquipo, $periodo);

		$this->render('equipo', array(
			'idEquipo'=>$idEquipo,
			'periodo'=>$periodo,
			'estado'=>$estado,
		));
	}

	public function actionGuardar()
	{
		if(!Yii::app()->request->isPostRequest)
			throw new CHttpException(400, 'Peticion no valida.');

		$idEquipo = isset($_POST['idEquipo']) ? (int)$_POST['idEquipo'] : 0;
		$periodo = isset($_POST['periodo']) ? $_POST['periodo'] : date('Y-m');
		$socios = isset($_POST['socios']) ? $_POST['socios'] : array();
		$pagados = isset($_POST['pagados']) ? $_POST['pagados'] : array();
		$idUsuario = Yii::app()->user->isGuest ? null : Yii::app()->user->getId();

		try {
			CuotaSocialPago::marcarPagosPeriodo($idEquipo, $periodo, $socios, $pagados, $idUsuario);
			Yii::app()->user->setFlash('success', 'Cuotas sociales actualizadas.');
		} catch(Exception $e) {
			Yii::app()->user->setFlash('error', $e->getMessage());
		}

		$this->redirect(array('equipo', 'idEquipo'=>$idEquipo, 'periodo'=>CuotaSocialPago::normalizarPeriodo($periodo)));
	}

	public function actionInforme()
	{
		$periodo = CuotaSocialPago::normalizarPeriodo(isset($_GET['periodo']) ? $_GET['periodo'] : date('Y-m'));
		$idEquipo = isset($_GET['idEquipo']) ? trim($_GET['idEquipo']) : '';
		$estadoFiltro = isset($_GET['estado']) ? trim($_GET['estado']) : '';
		$hayFiltro = $idEquipo !== '' || $estadoFiltro !== '';
		$informe = array('alDia'=>array(), 'pendientes'=>array(), 'noSocios'=>array());
		$dataProvider = null;

		if($hayFiltro) {
			$informe = CuotaSocialPago::getInformePeriodo($periodo, $idEquipo, $estadoFiltro);
			$rows = array_merge($informe['alDia'], $informe['pendientes'], $informe['noSocios']);
			$dataProvider = new CArrayDataProvider($rows, array(
				'keyField'=>'idJugador',
				'pagination'=>array('pageSize'=>50),
				'sort'=>array(
					'attributes'=>array('Nombre', 'Equipo', 'estado'),
					'defaultOrder'=>array('Equipo'=>CSort::SORT_ASC, 'Nombre'=>CSort::SORT_ASC),
				),
			));
		}

		$this->render('informe', array(
			'periodo'=>$periodo,
			'idEquipo'=>$idEquipo,
			'estadoFiltro'=>$estadoFiltro,
			'hayFiltro'=>$hayFiltro,
			'informe'=>$informe,
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionEstadoEquipo($idEquipo, $periodo)
	{
		$this->layout='//layouts/column1';
		$estado = CuotaSocialPago::getEstadoEquipo((int)$idEquipo, $periodo);
		$this->render('estadoEquipo', array(
			'estado'=>$estado,
		));
	}
}
