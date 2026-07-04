<?php

class ConexionesController extends Controller
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
				'actions'=>array('index','view', 'grabaconexion'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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
		$model=new Conexiones;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Conexiones']))
		{
			$model->attributes=$_POST['Conexiones'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->idConexion));
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

		if(isset($_POST['Conexiones']))
		{
			$model->attributes=$_POST['Conexiones'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->idConexion));
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
		$dataProvider=new CActiveDataProvider('Conexiones');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Conexiones('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Conexiones']))
			$model->attributes=$_GET['Conexiones'];

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
		$model=Conexiones::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='conexiones-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionGraba(){
		//$model = new Conexiones;
		//print $latitud;
		//print_r($_POST);
		//Yii::app()->end();
		/*$model->latitud = $_POST['latitud'];
		$model->longitud = $_POST['longitud'];
		$model->altitud = $_POST['altitud'];
		$model->horario = $_POST['horario'];*/
		/*$model->latitud = $_REQUEST['latitud'];
		$model->longitud = $_REQUEST['longitud'];
		$model->altitud = $_REQUEST['altitud'];
		$model->horario = $_REQUEST['horario'];*/
		/*$model->latitud = $latitud;
		$model->longitud = $longitud;
		$model->altitud = $altitud;
		$model->horario = $horario;*/
		$sql = "insert into conexiones (latitud, longitud, altitud, horario) values (:latitud, :longitud, :altitud, :horario)";

		$parameters = array(":latitud"=>$_POST['latitud'], ":longitud" => $_POST['longitud'], ":altitud" => $_POST['altitud'], ":horario" => $_POST['horario']);

		Yii::app()->db->createCommand($sql)->execute($parameters);
		/*if($model->save()){
			echo CJSON::encode(array('mensaje' => 'Se grabo correctamente' ));
		}else{
			echo CJSON::encode(array('mensaje' => 'No se pudo grabar' ));
		}*/
		//Yii::app()->end();
		
	}
}
