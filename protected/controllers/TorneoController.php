<?php

class TorneoController extends Controller
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
		//return array(array('CrugeAccessControlFilter'));
			
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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update', 'SaldoCaja'),
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
		$model=new Torneo;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Torneo']))
		{
			$model->attributes=$_POST['Torneo'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->idTorneo));
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

		if(isset($_POST['Torneo']))
		{
			$model->attributes=$_POST['Torneo'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->idTorneo));
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
		$dataProvider=new CActiveDataProvider('Torneo');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Torneo('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Torneo']))
			$model->attributes=$_GET['Torneo'];

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
		$model=Torneo::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='torneo-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionSaldoCaja(){
		$torneo = new Torneo;
		$torneo->DesdeFecha = date('Y-m-d');
		$torneo->HastaFecha = date('Y-m-d');
		
		if(isset($_POST['Torneo'])){
			$torneo->DesdeFecha = $_POST['Torneo']['DesdeFecha'];
			$torneo->HastaFecha = $_POST['Torneo']['HastaFecha'];
			$egresos = Egresos::model()->EgresosFecha($_POST['Torneo']['DesdeFecha'],$_POST['Torneo']['HastaFecha']);
			$ingresos = Ingresos::model()->IngresosFecha($_POST['Torneo']['DesdeFecha'],$_POST['Torneo']['HastaFecha']);
			$this->render('saldocaja',array(
				'torneo'=>$torneo,
				'ingresos'=>$ingresos,
				'egresos'=>$egresos,
			));
		}else{
			$this->render('saldocaja',array(
				'torneo'=>$torneo,
			));
		}
	}
    
    public function actionGetTorneos($estado = 'I'){
        $criteria = new CDbCriteria;
		if($estado != ''){
			$criteria->condition = "estado in('" . $estado . "')";
            $criteria->order = 'Nombre';
			//$criteria->params = array('estado'=>$estado);
			//echo $estado;
		}
		echo CJSON::encode(CHtml::listData(Torneo::model()->findAll($criteria),'idTorneo','Nombre')); 
	}
}
