<?php

class IngresosController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		/*return array(
			'accessControl', // perform access control for CRUD operations
		);*/
		return array(array('CrugeAccessControlFilter - reciboPublico'));

	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update','view','ingresosequipo','ArancelFecha','IngresoPortipo','reciboPdf','arqueoCaja','anular','resumenMensual'),
				'users'=>array('admin','oscarvogel','teo'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		date_default_timezone_set("America/Argentina/Buenos_Aires");
		$model=new Ingresos;
		$model->Fecha = date("Y-m-d");
		$model->Estado = 'VIGENTE';
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Ingresos']))
		{
			$model->attributes=$_POST['Ingresos'];
			$transaction = Yii::app()->db->beginTransaction();
			try {
				$model->Hora = date("H:i:s");
				$model->FechaAlta = date("Y-m-d H:i:s");
				$model->idUsuario = Yii::app()->user->id;
				$model->Estado = 'VIGENTE';
				$model->NumeroRecibo = Ingresos::siguienteNumeroRecibo();

				if(!$model->save())
					throw new CException('No se pudo registrar el pago.');

				$transaction->commit();
				$this->enviarMailRecibo($model);

				if(isset($_POST['btnAgregar'])){
					$this->redirect(array('create'));
				}else{
					$this->redirect(array('view','id'=>$model->idIngreso));
				}
			} catch(Exception $e) {
				if($transaction->active)
					$transaction->rollback();
				$model->addError('idIngreso', $e->getMessage());
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Ingresos']))
		{
			$model->attributes=$_POST['Ingresos'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		throw new CHttpException(400,'Los recibos no se borran. Use la anulacion para conservar trazabilidad.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Ingresos');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Ingresos('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ingresos']))
			$model->attributes=$_GET['Ingresos'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Ingresos::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'La página solicitada no existe.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='ingresos-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionIngresosEquipo(){
		$EquiposTorneo = new Equipostorneo;
		
		if(isset($_POST['Equipostorneo'])){
			$ingresos = Ingresos::model()->IngresoEquipo($_POST['Equipostorneo']['idEquipo']);
			$this->render('IngresosEquipo', array(
				'EquiposTorneo'=>$EquiposTorneo,
				'Ingresos'=>$ingresos,
			));
		}else{
			$this->render('IngresosEquipo', array(
				'EquiposTorneo'=>$EquiposTorneo,
			));
		}
	}

	public function actionArancelFecha(){
		$ingresos = new Ingresos;
		
		if(isset($_POST['Ingresos'])){
			$datos = Ingresos::model()->getPagosFecha($_POST['Ingresos']['Fecha']);
			$this->render('ArancelFecha', array(
				'ingresos' => $ingresos,
				'dataProvider' => $datos,
			));
		}else{
			$this->render('ArancelFecha', array(
				'ingresos' => $ingresos,
			));
		}
	}
	
	public function actionIngresoPortipo(){
		$ingresos = new Ingresos;
		if(isset($_POST['Ingresos'])){
			$datos = Ingresos::model()->getIngresosTipo($_POST['Ingresos']['idEquipo'], $_POST['Ingresos']['idConcepto']);
			$this->render('IngresoPorTipo', array(
				'ingresos' => $ingresos,
				'dataProvider' => $datos,
			));
		}else{
			$this->render('IngresoPorTipo', array(
				'ingresos' => $ingresos,
			));
		}
	}

	public function actionReciboPdf($id)
	{
		$model = $this->loadModel($id);
		$this->renderReciboPdf($model);
	}

	public function actionReciboPublico($token)
	{
		$model = Ingresos::findByReciboToken($token);
		if($model === null)
			throw new CHttpException(404, 'El recibo solicitado no existe o el enlace no es valido.');

		$this->renderReciboPdf($model);
	}

	private function renderReciboPdf($model)
	{
		$html = $this->renderPartial('reciboPdf', array('model'=>$model), true);

		$pdf = Yii::app()->ePdf->mpdf('', 'A5');
		$pdf->WriteHTML($html);
		$numero = $model->NumeroRecibo ? $model->NumeroRecibo : $model->idIngreso;
		$filename = 'recibo-' . str_pad($numero, 8, '0', STR_PAD_LEFT) . '.pdf';
		$pdf->Output($filename, 'I');
	}

	public function actionArqueoCaja()
	{
		$desde = isset($_GET['desde']) ? $_GET['desde'] : date('Y-m-d');
		$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-d');
		$idUsuario = isset($_GET['idUsuario']) ? $_GET['idUsuario'] : null;

		$dataProvider = Ingresos::getArqueoCaja($desde, $hasta, $idUsuario);
		$totalVigente = Ingresos::getTotalArqueoCaja($desde, $hasta, $idUsuario);

		$this->render('arqueoCaja', array(
			'desde'=>$desde,
			'hasta'=>$hasta,
			'idUsuario'=>$idUsuario,
			'dataProvider'=>$dataProvider,
			'totalVigente'=>$totalVigente,
		));
	}

	public function actionResumenMensual()
	{
		$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
		$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');
		$resumen = Ingresos::getResumenMensual($anio, $mes);

		$this->render('resumenMensual', array(
			'resumen'=>$resumen,
			'anio'=>$resumen['anio'],
			'mes'=>$resumen['mes'],
		));
	}

	public function actionAnular($id)
	{
		$model = $this->loadModel($id);
		if(isset($_POST['motivo'])) {
			if($model->anular($_POST['motivo']))
				$this->redirect(array('view','id'=>$model->idIngreso));
		}

		$this->render('anular', array('model'=>$model));
	}

	private function enviarMailRecibo($model)
	{
		if($model->Equipos === null || $model->Equipos->Correo == '')
			return;

		$to = $model->Equipos->Correo;
		$subject = 'Registro de pago';
		$message = 'Se registro un pago de ' . $model->Monto .
			' por ' . $model->Conceptos->Nombre . ' Numero de recibo ' . $model->NumeroRecibo .
			' Obs: ' . $model->Detalle;
		$headers = 'From: secretaria@veteranoslgsm.com.ar' . "\r\n" .
			'Reply-To: secretaria@veteranoslgsm.com.ar' . "\r\n" ;
		@mail($to, $subject, $message, $headers);
	}
}
