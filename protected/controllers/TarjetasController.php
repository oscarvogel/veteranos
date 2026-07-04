<?php

class TarjetasController extends Controller
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
				'actions'=>array('index','view','consulta','verJugadorFecha','tarjetasequipo','FairPlay','TarjetasAmarillasEquipoTorneo'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','vertarjetas'),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($idFixture, $idEquipo)
	{
		$model=new Tarjetas;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Tarjetas']))
		{
			$model->attributes=$_POST['Tarjetas'];
			$model->idEquipo = $idEquipo;
			if($model->save())
				$this->render('create',array(
					'model'=>$model,
					'idFixture'=>$idFixture,
					'idEquipo'=>$idEquipo,
				));
		}

		$this->render('create',array(
			'model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id, $idFixture, $idEquipo)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Tarjetas']))
		{
			$model->attributes=$_POST['Tarjetas'];
			$model->idEquipo = $idEquipo;
			if($model->save())
				$this->redirect(array('vertarjetas','idFixture'=>$idFixture, 'idEquipo'=>$idEquipo));
		}

		$this->render('update',array(
			'model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
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
		$dataProvider=new CActiveDataProvider('Tarjetas');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Tarjetas('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tarjetas']))
			$model->attributes=$_GET['Tarjetas'];

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
		$model=Tarjetas::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='tarjetas-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionVerTarjetas($idFixture, $idEquipo){
		$model = Tarjetas::model()->VerTarjetas($idFixture, $idEquipo);
		
		$this->render('VerTarjetas', array(
			'model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
		));
	}
	
	
	public function actionConsulta(){

		if(isset($_POST['Fixture'])){
			$idTorneo = $_POST['Fixture']['idTorneo'];
			$Fecha = $_POST['Fixture']['Fecha'];
		}else{
			$idTorneo = 1;
			$Fecha = "0000-00-00";
		}
		
		$model = new Fixture;
		$modelAmarillas = Tarjetas::model()->ConsultaTarjetasAmarillasTorneo($idTorneo, $Fecha);
		$modelRojas = Tarjetas::model()->ConsultaTarjetasRojas($idTorneo, $Fecha);

		if(isset($_POST['btnExcel'])){
			$this->renderPartial('excel', array(
				'modelAmarillas'=>$modelAmarillas,
				'modelRojas'=>$modelRojas,
				'model'=>$model,
			));

		}else{
			$this->render('consulta', array(
				'modelAmarillas'=>$modelAmarillas,
				'modelRojas'=>$modelRojas,
				'model'=>$model,
			));
		}
	}
	
	public function actionverJugadorFecha($idJugador, $amarilla){
		$model = Tarjetas::model()->ConsultaTarjetasJugador($idJugador, $amarilla);
		
		$this->render('verJugadorFecha', array(
			'model'=>$model,
		));
	}
	
	public function actionTarjetasEquipo(){
		$model = new Equipostorneo;
		
		if(isset($_POST['Equipostorneo'])){
			$torneo = Torneo::model()->findByPk($_POST['Equipostorneo']['idTorneo']);
			$modelAmarillas = Tarjetas::model()->TarjetasEquipo($_POST['Equipostorneo']['idTorneo'],$_POST['Equipostorneo']['idEquipo'],'A', $torneo);
			$modelRojas = Tarjetas::model()->TarjetasEquipo($_POST['Equipostorneo']['idTorneo'],$_POST['Equipostorneo']['idEquipo'],'R', $torneo);
			$this->render('TarjetasEquipo',array(
				'model' => $model,
				'modelAmarillas' => $modelAmarillas,
				'modelRojas' => $modelRojas
			));
		}else{
			$this->render('TarjetasEquipo',array(
				'model' => $model,
			));
		}
	}
	
	public function actionFairPlay(){
		$model = new Torneo;
		
		if(isset($_POST['Torneo'])){
			$Tarjetas = Tarjetas::model()->FairPlay($_POST['Torneo']['idTorneo']);
			if(isset($_POST['btnExcel'])){
				$this->renderPartial('FairPlayExcel',array(
					'model' => $model,
					'Tarjetas' => $Tarjetas
				));
			}else{
				$this->render('FairPlay',array(
					'model' => $model,
					'Tarjetas' => $Tarjetas
				));
			}
		}else{
			$this->render('FairPlay',array(
				'model' => $model,
			));
		}
	}
public function actionTarjetasAmarillasEquipoTorneo()
{
    // Recibe idEquipo e idTorneo (ej. desde parámetros GET)
    $idEquipo = isset($_GET['idEquipo']) ? (int)$_GET['idEquipo'] : null;
    $idTorneo = isset($_GET['idTorneo']) ? (int)$_GET['idTorneo'] : null;

    if (!$idEquipo || !$idTorneo) {
        throw new CHttpException(400, 'Por favor, especifique un equipo y un torneo.');
    }

    // Obtenemos los modelos para mostrar sus nombres (opcional, pero útil para la vista)
    $equipoModel = Equipos::model()->findByPk($idEquipo);
    $torneoModel = Torneo::model()->findByPk($idTorneo); // Asumiendo que tienes un modelo Torneo

    if (!$equipoModel || !$torneoModel) {
        throw new CHttpException(404, 'El equipo o torneo especificado no existe.');
    }

    $criteria = new CDbCriteria;

    // Group by player and count yellow cards
    $criteria->select = 't.idJugador, COUNT(*) as total_amarillas, MAX(Fixture.Fecha) as ultima_fecha';
    $criteria->group = 't.idJugador';
    $criteria->compare('t.idEquipo', $idEquipo);
    $criteria->addCondition('t.Amarilla = 1');

    // Include necessary relations for filtering and displaying data
    $criteria->with = array(
        'Fixture' => array(
            'condition' => 'Fixture.idTorneo = :idTorneo',
            'params' => array(':idTorneo' => $idTorneo),
        ),
        'Jugador', // For player name
        'Equipo',  // For team name
    );

    $dataProvider = new CActiveDataProvider('Tarjetas', array(
        'criteria' => $criteria,
        'pagination' => array(
            'pageSize' => 25,
        ),
        'sort' => array(
            'defaultOrder' => 'total_amarillas DESC, Jugador.nombre ASC', // Sort by card count and player name
        ),
    ));

    $this->render('tarjetasAmarillasEquipoTorneo', array(
        'dataProvider' => $dataProvider,
        'equipoModel' => $equipoModel,
        'torneoModel' => $torneoModel,
    ));
}
}
