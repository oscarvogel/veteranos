<?php

class FixtureController extends Controller
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
		return array(array('CrugeAccessControlFilter'));

	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('ConsultaFixture','ConsultaAsignaciones', 'getHora'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','creafixture','create','update','AsignaCanchaArbitros',
						'index','view','ModificaAsignaciones','actualiza','verCanchas',
						'CopiaFixture'),
				'users'=>array('admin','oscarvogel'),
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
		$goleador = new Goles;
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'goleador'=>$goleador,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		Yii::import('ext.multimodelform.MultiModelForm');
			
		$model=new Fixture;
		
		$goleador = new Goles;
		$validatedMembers = array(); //ensure an empty array

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Fixture']))
		{
			$model->attributes=$_POST['Fixture'];
			$model->Hora = '00:00';
			if(isset($_POST['btnGuardaPuntos']))
				$this->AsignaPuntos($model);
			if($model->save()){
				$masterValues = array ('idFixture'=>$model->idFixture);
				if (MultiModelForm::save($goleador, $validatedMembers, $deleteMembers, $masterValues))
					$this->redirect(array('admin'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'goleador'=>$goleador,
			'validatedMembers'=>$validatedMembers,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		Yii::import('ext.multimodelform.MultiModelForm');
			
		$model=$this->loadModel($id);

		$goleador = new Goles;
		$validatedMembers = array(); //ensure an empty array
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Fixture']))
		{
			$model->attributes=$_POST['Fixture'];
			if(isset($_POST['btnGuardaPuntos']))
				$this->AsignaPuntos($model);
			$masterValues = array ('idFixture'=>$model->idFixture);
			if(MultiModelForm::save($goleador, $validatedMembers, $deleteMembers, $masterValues) && $model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
			'goleador'=>$goleador,
        	'validatedMembers' => $validatedMembers,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Petición no Válida. Por favor, no repita esta solicitud de nuevo.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Fixture');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Fixture('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Fixture']))
			$model->attributes=$_GET['Fixture'];

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
		$model=Fixture::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='fixture-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	protected function AsignaPuntos($model){
		if($model->GolLocal > $model->GolVisitante){
			$model->PuntosLocal = 3;
			$model->PuntosVisitante = 0;
		}elseif($model->GolVisitante > $model->GolLocal){
			$model->PuntosLocal = 0;
			$model->PuntosVisitante = 3;
		}else{
			$model->PuntosLocal = 1;
			$model->PuntosVisitante = 1;
		}
	}


	public function actionCreaFixture(){
		Yii::import("ext.armafixture.*");
		$torneo = new Torneo;
		
		if(isset($_POST['Torneo'])){
			$model = Torneo::Model()->findByPk($_POST['Torneo']['idTorneo']);
			if($model->Estado != 'A'){
				$this->render('CreaFixture', array(
					'torneo'=>$torneo,
					'mensaje'=>'Ya Inicio Torneo o se lo dio de baja o esta suspendido. No se lo puede volver a generar',
				));
			}else{
				$equipos 	= Equipos::EquiposTorneo($_POST['Torneo']['idTorneo']);
				$objFix     = new ArmaFixture($equipos);
				$objFix->setAleatorio(true);
				// No es necesario para true pero es a modo de ejemplo.
	
				$objFix->tablaDeCruces();
	
				$cruces     = $objFix->getCruces();
				
				Fixture::Guardar($_POST['Torneo']['idTorneo'], $objFix, $cruces, $model->Inicio);
				$this->render('CreaFixture', array(
					'torneo'=>$torneo,
					'cruces'=>$cruces,
					'fixture'=>$objFix
				));
			}
		}else{
			$this->render('CreaFixture', array(
				'torneo'=>$torneo,
			));
		}
	}
	
	
	public function actionConsultaFixture(){
		$torneo = new Torneo;
		
		if(isset($_POST['Torneo'])){
			$partidos = Fixture::model()->ConsultaFixture($_POST['Torneo']['idTorneo']);
			if(isset($_POST['btnExcel'])){
				$this->renderPartial('ConsultaFixtureExcel', array(
					'torneo'=>$torneo,
					'partidos'=>$partidos,
				));
			}else{
				$this->render('ConsultaFixture', array(
					'torneo'=>$torneo,
					'partidos'=>$partidos,
				));
			}
		}else{
			$this->render('ConsultaFixture', array(
				'torneo'=>$torneo,
			));
		}
		
	}


	public function actionAsignaCanchaArbitros(){
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			$partidos = Fixture::model()->ConsultaFecha($_POST['Fixture']['idTorneo'], $_POST['Fixture']['NFecha']);
			$this->render('AsignacionCanchaArbitros',array(
				'fixture'=>$fixture,
				'partidos'=>$partidos,
			));
				
		}else{
			$this->render('AsignacionCanchaArbitros',array(
				'fixture'=>$fixture,
			));
		}
	}

	
	public function actionConsultaAsignaciones(){
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			$fixture = Fixture::model()->find('Fecha=:Fecha', array('Fecha'=>$_POST['Fixture']['Fecha']));
			
			$partidos = Fixture::model()->ConsultaAsignaciones($_POST['Fixture']['Fecha']);
			if(isset($_POST['btnExcel'])){
				$this->renderPartial('excel', array(
					'fixture'=>$fixture,
					'partidos'=>$partidos));
			}else{
				$this->render('ConsultaAsignaciones',array(
					'fixture'=>$fixture,
					'partidos'=>$partidos,
				));
			}
		}else{
			$this->render('ConsultaAsignaciones', array(
				'fixture'=>$fixture,
			));
		}
	}


	public function actionModificaAsignaciones(){
			
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			
			$fixture = Fixture::model()->find('Fecha=:Fecha', array('Fecha'=>$_POST['Fixture']['Fecha']));
			$criteria=new CDbCriteria;
			$criteria->condition = "Fecha=:Fecha";
			$criteria->params = array('Fecha'=>$_POST['Fixture']['Fecha']);
			$criteria->order	= 'idCancha desc, Hora';
			
			$dataProvider = new CActiveDataProvider('Fixture', array(
				'criteria'=>$criteria, 'pagination'=>array('pageSize'=>30,),
			));
			
			$this->render('ModificaAsignaciones',array(
				'dataProvider'=>$dataProvider,
				'fixture'=>$fixture,
			));
		}else{
			$this->render('ModificaAsignaciones',array(
				'fixture'=>$fixture,
			));
		}
	}
	
	
	public function actionActualiza(){
		//print_r($_POST);
		//Yii::app()->end();
		$model = $this->loadModel($_POST['pk']);
		if($_POST['name'] == 'idCancha'){
			$model->idCancha	= $_POST['value'];
		}elseif($_POST['name'] == 'idArbitro'){
			$model->idArbitro	= $_POST['value'];
		}elseif($_POST['name'] == 'idLinea1'){
			$model->idLinea1	= $_POST['value'];
		}elseif($_POST['name'] == 'idLinea2'){
			$model->idLinea2	= $_POST['value'];
		}elseif($_POST['name'] == 'Hora'){
			$model->Hora 		= $_POST['value'];
		}
		$model->save();
	}
	
	
	public function actionCambiaFecha(){
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			Fixture::model()->CambiaFecha($_POST['Fixture']['Fecha'],$_POST['Fixture']['NFecha'],$_POST['Fixture']['CambiaFecha'],$_POST['Fixture']['idTorneo']);
			$this->redirect(Yii::app()->createUrl('/fixture/admin'));
		}else{
			$this->render('CambiaFecha', array(
				'fixture'=>$fixture,
			));
		}
	}
	
	public function actionverCanchas($idEquipo, $idTorneo){
		$fixture = Fixture::model()->verCanchasEquipo($idEquipo, $idTorneo);
		$equipo = Equipos::model()->findByPk($idEquipo);
		
		$this->render('verCanchas', array(
			'fixture'=>$fixture,
			'idEquipo'=>$idEquipo,
			'equipo'=>$equipo,
		));
	}
	
	public function actionCopiaFixture(){
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			Fixture::model()->CopiaFixture($_POST['Fixture']['idTorneo'],$_POST['idTorneoDestino'],$_POST['DesdeFecha'],$_POST['HastaFecha']);
			$this->redirect(Yii::app()->createUrl('/fixture/admin'));
		}else{
			$this->render('CopiaFixture',array(
				'fixture'=>$fixture,
			));
		}
	}
	
	public function actiongetHora(){
		echo CJSON::encode(CHtml::listData(Horas::model()->findAll(), 'Hora', 'Hora')); 
	}

	
	public function actionCargaResultadosFecha(){
			
		$fixture = new Fixture;
		$fixture->Fecha = date("Y-m-d");
		if(isset($_POST['Fixture'])){
			
			$fixture = Fixture::model()->find('Fecha=:Fecha', array('Fecha'=>$_POST['Fixture']['Fecha']));
			$criteria=new CDbCriteria;
			$criteria->condition = "Fecha=:Fecha";
			$criteria->params = array('Fecha'=>$_POST['Fixture']['Fecha']);
			$criteria->order	= 'idCancha, Hora';
			
			$dataProvider = new CActiveDataProvider('Fixture', array(
				'criteria'=>$criteria, 
                'pagination'=>array('pageSize'=>40,),
			));
			
			$this->render('CargaResultadosFecha',array(
				'dataProvider'=>$dataProvider,
				'fixture'=>$fixture,
			));
		}else{
			$this->render('CargaResultadosFecha',array(
				'fixture'=>$fixture,
			));
		}
	}


	public function actionAdelantaFecha(){
		$fixture = new Fixture;
		
		if(isset($_POST['Fixture'])){
			Fixture::model()->AdelantaFecha($_POST['Fixture']['NFecha'],$_POST['Fixture']['idTorneo']);
			$this->redirect(Yii::app()->createUrl('/fixture/admin'));
		}else{
			$this->render('AdelantaFecha', array(
				'fixture'=>$fixture,
			));
		}
	}

	public function actionConsultaFixtureJson($idTorneo = 1){
		$torneo = new Torneo;
		
		$datosJSON = array();
		$partidos = Fixture::model()->ConsultaFixture($idTorneo);

		foreach ($partidos as $partido) {
			$datosJSON[$partido->NFecha][Equipos::model()->findByPk($partido->Local)->Nombre] = $partido->Local;
			$datosJSON[$partido->NFecha][Equipos::model()->findByPk($partido->Visitante)->Nombre] = $partido->Visitante;
		}
		echo CJSON::encode($datosJSON);		
	}

	public function actionConsultaAsignacionesJson($fecha){

		$datosJSON = array();
		$partidos = Fixture::model()->ConsultaAsignaciones($fecha);

		foreach ($partidos as $partido) {
			$nombreCancha = Canchas::model()->findByPk($partido->idCancha)->Nombre;
			$datosJSON[$nombreCancha][Equipos::model()->findByPk($partido->Local)->Nombre] = $partido->Local;
			$datosJSON[$nombreCancha][Equipos::model()->findByPk($partido->Visitante)->Nombre] = $partido->Visitante;
			$datosJSON[$nombreCancha][$partido->Hora] = $partido->Hora;
		}
		echo CJSON::encode($datosJSON);		
	}
}
