<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
			'coco'=>array(
                'class'=>'CocoAction',
            ),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$dataProvider=new CActiveDataProvider('Articulos', array(
			'criteria'=>array(
				'order'=>'FechaPublicacion Desc',
			),
			'pagination'=>array(
				'pageSize'=>6,
			),
		));
		$criteria = new CDbCriteria;
		$criteria->condition = "Estado=:estado";
		$criteria->params = array(':estado'=>'I');
		$criteria->order = 'idTorneo DESC';
		$torneos = Torneo::model()->findAll($criteria);
		$posicionesTorneos = array();
		foreach($torneos as $torneo){
			$fixture = new Fixture;
			$posicionesTorneos[] = array(
				'torneo'=>$torneo,
				'posiciones'=>$fixture->TablaPosiciones($torneo->idTorneo),
			);
		}

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'posicionesTorneos'=>$posicionesTorneos,
		));

	}

	public function actionFixture($idTorneo = null, $fecha = null)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "Estado in ('I','A')";
		$criteria->order = 'Inicio DESC, idTorneo DESC';
		$torneosDisponibles = Torneo::model()->findAll($criteria);

		if (empty($torneosDisponibles)) {
			throw new CHttpException(404, 'No hay torneos iniciados o activos en este momento.');
		}

		$mapaTorneos = array();
		foreach ($torneosDisponibles as $t) {
			$mapaTorneos[(int)$t->idTorneo] = $t->Nombre;
		}

		if ($idTorneo === null || !isset($mapaTorneos[(int)$idTorneo])) {
			$idTorneo = (int)$torneosDisponibles[0]->idTorneo;
		}

		$torneo = Torneo::model()->findByPk($idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'El torneo solicitado no existe.');
		}

		$this->pageTitle = 'Fixture ' . $torneo->Nombre . ' - ' . Yii::app()->name;

		$partidos = Fixture::model()->ConsultaFixture($idTorneo);

		// Fechas disponibles para este torneo
		$fechasDisponibles = array();
		foreach ($partidos as $p) {
			$n = (int)$p->NFecha;
			if (!in_array($n, $fechasDisponibles, true)) {
				$fechasDisponibles[] = $n;
			}
		}
		sort($fechasDisponibles);

		$fechaSeleccionada = null;
		if ($fecha !== null && ctype_digit((string)$fecha)) {
			$fecha = (int)$fecha;
			if (in_array($fecha, $fechasDisponibles, true)) {
				$fechaSeleccionada = $fecha;
			}
		}

		if ($fechaSeleccionada !== null) {
			$filtrados = array();
			foreach ($partidos as $p) {
				if ((int)$p->NFecha === $fechaSeleccionada) {
					$filtrados[] = $p;
				}
			}
			$partidos = $filtrados;
		}

		$this->render('fixture', array(
			'torneo' => $torneo,
			'torneosDisponibles' => $mapaTorneos,
			'partidos' => $partidos,
			'fechasDisponibles' => $fechasDisponibles,
			'fechaSeleccionada' => $fechaSeleccionada,
			'idTorneo' => $idTorneo,
		));
	}

	public function actionFixturePdf($idTorneo, $fecha = null)
	{
		$torneo = Torneo::model()->findByPk((int)$idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'El torneo solicitado no existe.');
		}
		if (!in_array($torneo->Estado, array('I', 'A'), true)) {
			throw new CHttpException(404, 'El torneo no está iniciado ni activo.');
		}

		$partidos = Fixture::model()->ConsultaFixture((int)$idTorneo);
		if (empty($partidos)) {
			throw new CHttpException(404, 'No hay partidos cargados para este torneo.');
		}

		$fechaSeleccionada = null;
		if ($fecha !== null && ctype_digit((string)$fecha)) {
			$fechaSeleccionada = (int)$fecha;
		}

		if ($fechaSeleccionada !== null) {
			$filtrados = array();
			foreach ($partidos as $p) {
				if ((int)$p->NFecha === $fechaSeleccionada) {
					$filtrados[] = $p;
				}
			}
			if (empty($filtrados)) {
				throw new CHttpException(404, 'No hay partidos cargados para la fecha solicitada.');
			}
			$partidos = $filtrados;
		}

		$slug = preg_replace('/[^a-z0-9]+/i', '_', strtolower($torneo->Nombre));
		$nombreArchivo = 'fixture_' . $slug
			. ($fechaSeleccionada !== null ? '_fecha_' . $fechaSeleccionada : '')
			. '.pdf';

		$pdf = Yii::app()->ePdf->mpdf('', 'A4', 0, '', 12, 12, 14, 14, 9, 9, 'P');
		$titulo = 'Fixture ' . $torneo->Nombre
			. ($fechaSeleccionada !== null ? ' - Fecha ' . $fechaSeleccionada : '');
		$pdf->SetTitle($titulo);
		$pdf->SetAuthor(Yii::app()->name);

		$html = $this->renderPartial('fixture_pdf', array(
			'torneo' => $torneo,
			'partidos' => $partidos,
			'fechaSeleccionada' => $fechaSeleccionada,
		), true);

		$pdf->WriteHTML($html);
		$pdf->Output($nombreArchivo, 'I');
		Yii::app()->end();
	}

	public function actionFixtureSuper()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "Estado in ('I','A') and Nombre like '%Super Veteranos%'";
		$criteria->order = 'Inicio DESC, idTorneo DESC';
		$torneo = Torneo::model()->find($criteria);
		if ($torneo === null) {
			throw new CHttpException(404, 'No hay Super Veteranos iniciado o activo en este momento.');
		}
		$this->redirect(array('site/fixture', 'idTorneo' => (int)$torneo->idTorneo));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact',Yii::t('app','Thank you for contacting us. We will respond to you as soon as possible.'));
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
