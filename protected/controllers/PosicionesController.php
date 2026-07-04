<?php

class PosicionesController extends Controller
{
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
				'actions'=>array('index','resultados','fichapartido'),
				'users'=>array('*'),
			),
		);
	}
	
	
	public function actionIndex()
	{
		$model = Torneo::model()->findByPk(1);
		
		if(isset($_POST['Torneo'])){
			$datos = Fixture::model()->TablaPosiciones($_POST['Torneo']['idTorneo']);
			$posiciones = Posicionestorneo::model()->PosicionFinal($_POST['Torneo']['idTorneo']);
		}else{
			$posiciones = Posicionestorneo::model()->PosicionFinal(0);
			$datos = Fixture::model()->TablaPosiciones();
		}
		if(isset($_POST['btnExcel'])){
			$this->renderPartial('indexExcel', array(
				'datos'=>$datos,
				'model'=>$model,
			));
		}else{
			$this->render('index', array(
				'datos'=>$datos,
				'model'=>$model,
				'posiciones'=>$posiciones,
			));
		}
	}

	public function actionResultados(){
		$model = new Fixture;
		if(isset($_POST['Fixture'])){
			$torneo = Torneo::model()->findByPk($_POST['Fixture']['idTorneo']);
			$datos = Fixture::model()->ConsultaFecha($_POST['Fixture']['idTorneo'], $_POST['Fixture']['Fecha']);
		}else{
			$datos = Fixture::model()->ConsultaFecha();
			$torneo = Torneo::model()->findByPk(0);
		}
		
		if(isset($_POST['btnExcel'])){
			$this->renderPartial('resultadosExcel',array(
				'datos'=>$datos,
				'model'=>$model,
				'torneo'=>$torneo,
			));
		}else{
			$this->render('resultados',array(
				'datos'=>$datos,
				'model'=>$model,
				'torneo'=>$torneo,
			));
		}
	}
	

	public function actionVerFichaPartido($idFixture, $idEquipo, $idTorneo){
		//$model = Fixture::model()->findByPk($idFixture);
		$model = new Tarjetas;
		$torneo = Torneo::model()->findByPk($idTorneo);
		
		if(isset($_POST['Tarjetas'])){
			$model->attributes = $_POST['Tarjetas'];
			$model->idEquipo = $idEquipo;
			//print_r($_POST);
			//Yii::app()->end();
			if($model->validate()){
				if($model->save()){
					$this->redirect(array('VerFichaPartido', 
						'idFixture'=>$idFixture,
						'idEquipo'=>$idEquipo,
						'idTorneo'=>$idTorneo,
					));
				}
			}
		}
		
		//Goles del partido
		$criteria = new CDbCriteria;
		$criteria->condition = "idFixture = " . $idFixture;
		$goles = Goles::model()->findAll($criteria);
		
		//Tarjetas del partido
		$criteria = new CDbCriteria;
		$criteria->condition = "idFixture = " . $idFixture;
		$tarjetas = Tarjetas::model()->findAll($criteria);
		
		
		$this->render('VerFichaPartido', array(
			'idFixture'=>$idFixture,
			'goles'=>$goles,
			'tarjetas'=>$tarjetas,
			'model'=>$model,
			'idEquipo'=>$idEquipo,
			'torneo'=>$torneo,
		)); 
	}

	public function actionPosicionesJson($idTorneo = 1)	{
		header('Content-type: application/json');
		$model = Torneo::model()->findByPk($idTorneo);

		$posiciones = Posicionestorneo::model()->PosicionFinal($idTorneo);
		$datos = Fixture::model()->TablaPosiciones($idTorneo);

		echo CJSON::encode($datos);
		Yii::app()->end();
	}

	
	public function actionResultadosJson($idTorneo = 1, $fecha){
		$model = new Fixture;
		$datos = Fixture::model()->ConsultaFecha($idTorneo, $fecha);
		$datosJSON = array();

		foreach ($datos as $dato) {
			$datosJSON[Equipos::model()->findByPk($dato->Local)->Nombre] = $dato->GolLocal;
			$datosJSON[Equipos::model()->findByPk($dato->Visitante)->Nombre] = $dato->GolVisitante;
		}
		echo CJSON::encode($datosJSON);
		Yii::app()->end();
				
	}
    
    public function actionPosicionesTorneos(){
		header('Content-type: application/json');
		$model = Torneo::model()->findByPk(37);

		$posiciones = Posicionestorneo::model()->PosicionFinal(37);
		$datos = Fixture::model()->TablaPosiciones(37);

		$model = Torneo::model()->findByPk(38);

		$posiciones = Posicionestorneo::model()->PosicionFinal(38);
		$datos1 = Fixture::model()->TablaPosiciones(38);
        $resultado = array_merge($datos, $datos1);
        
		echo CJSON::encode($datos);
		Yii::app()->end();        
    }

}