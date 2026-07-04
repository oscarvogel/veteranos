<?php

class EquiposController extends Controller
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
				'actions'=>array('index','view','ListaBuenaFe','SelectEquipos','GetEquipos'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
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
		$jugador = new Jugador;
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'jugador'=>$jugador,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		Yii::import('ext.multimodelform.MultiModelForm');
		
		$model=new Equipos;
		
		$jugador = new Jugador;
		$validatedMembers = array(); //ensure an empty array
		
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Equipos']))
		{
			$model->attributes=$_POST['Equipos'];
			if( MultiModelForm::validate($jugador,$validatedMembers,$deleteItems) && $model->save()){
				//the value for the foreign key 'groupid'
             	$masterValues = array ('idEquipo'=>$model->idEquipo);
				if (MultiModelForm::save($jugador,$validatedMembers,$deleteMembers,$masterValues))
					$this->redirect(array('view','id'=>$model->idEquipo));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'jugador'=>$jugador,
        	'validatedMembers' => $validatedMembers,
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
		
		$jugador = new Jugador;
		$validatedMembers = array(); //ensure an empty array
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Equipos']))
		{
			$model->attributes=$_POST['Equipos'];
			//the value for the foreign key 'groupid'
        	$masterValues = array ('idEquipo'=>$model->idEquipo);

			if($model->save()
				&& (!$this->hayCambiosJugadoresEquipo($_POST)
					|| MultiModelForm::save($jugador, $validatedMembers, $deleteMembers, $masterValues)))
				$this->redirect(array('view','id'=>$model->idEquipo));
		}

		$this->render('update',array(
			'model'=>$model,
			//submit the member and validatedItems to the widget in the edit form
        	'jugador'=>$jugador,
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
		$dataProvider=new CActiveDataProvider('Equipos');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Equipos('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Equipos']))
			$model->attributes=$_GET['Equipos'];

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
		$model=Equipos::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='equipos-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	private function hayCambiosJugadoresEquipo($post)
	{
		if(!isset($post['Jugador']) || !is_array($post['Jugador']))
			return false;

		$jugadorPost = $post['Jugador'];

		if(isset($jugadorPost['n__']) && is_array($jugadorPost['n__']))
		{
			foreach($jugadorPost['n__'] as $attributes)
			{
				if($this->tieneDatosJugadorEquipo($attributes))
					return true;
			}
		}

		$postedIds = array();
		if(isset($jugadorPost['u__']) && is_array($jugadorPost['u__']))
		{
			foreach($jugadorPost['u__'] as $idx => $attributes)
			{
				$idJugador = isset($jugadorPost['pk__'][$idx]['idJugador']) ? $jugadorPost['pk__'][$idx]['idJugador'] : null;
				if($idJugador === null || $idJugador === '')
					return true;

				$postedIds[] = (string)$idJugador;
				$jugador = Jugador::model()->findByPk($idJugador);
				if($jugador === null)
					return true;

				foreach(array('Nombre', 'Clase', 'DNI') as $campo)
				{
					$valorPost = isset($attributes[$campo]) ? trim((string)$attributes[$campo]) : '';
					$valorActual = trim((string)$jugador->$campo);
					if($valorPost !== $valorActual)
						return true;
				}
			}
		}

		if(isset($jugadorPost['pk__']) && is_array($jugadorPost['pk__']))
		{
			foreach($jugadorPost['pk__'] as $pk)
			{
				$idJugador = isset($pk['idJugador']) ? (string)$pk['idJugador'] : '';
				if($idJugador !== '' && !in_array($idJugador, $postedIds, true))
					return true;
			}
		}

		return false;
	}

	private function tieneDatosJugadorEquipo($attributes)
	{
		if(!is_array($attributes))
			return false;

		foreach(array('Nombre', 'Clase', 'DNI') as $campo)
		{
			if(isset($attributes[$campo]) && trim((string)$attributes[$campo]) !== '')
				return true;
		}

		return false;
	}
	
	
	public function actionListaBuenaFe(){
		
		$model = new Equipostorneo;
		
		if(isset($_POST['Equipostorneo'])){
			$idTorneo = $_POST['Equipostorneo']['idTorneo'];
			$idEquipo = $_POST['Equipostorneo']['idEquipo'];
			$torneo = Torneo::model()->findByPk($idTorneo);
			
			$jugadores = Jugador::model()->ListaBuenaFe($idTorneo, $idEquipo, $torneo);
			$equipo = Equipos::model()->findByPk($idEquipo);

			if(isset($_POST['btnExcel'])){
				/*Yii::app()->request->sendFile('jugador.xls',
						$this->renderPartial('excel', array(
							'jugadores'=>$jugadores,
							'model'=>$model
						))
					);*/

				$this->renderPartial('excel', array(
							'jugadores'=>$jugadores,
							'model'=>$model,
							'equipo'=>$equipo));
			}elseif(isset($_POST['btnLista'])){
                $mpdfErrorReporting = error_reporting();
                $deprecatedLevels = 0;
                if (defined('E_DEPRECATED')) {
                    $deprecatedLevels |= E_DEPRECATED;
                }
                if (defined('E_USER_DEPRECATED')) {
                    $deprecatedLevels |= E_USER_DEPRECATED;
                }
                if ($deprecatedLevels) {
                    error_reporting($mpdfErrorReporting & ~$deprecatedLevels);
                }

                $mPDF1 = Yii::app()->ePdf->mpdf('', 'A4');
                $mPDF1->SetTitle('Lista de buena fe - ' . $equipo->Nombre);
                $mPDF1->WriteHTML($this->renderPartial('pdf', array(
                    'jugadores'=>$jugadores,
                    'model'=>$model,
                    'torneo'=>$torneo,
                    'equipo'=>$equipo,
                ), true));

                $filename = 'lista-buena-fe-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($equipo->Nombre)) . '.pdf';
                $mPDF1->Output($filename, 'D');
                Yii::app()->end();
			}elseif(isset($_POST['btnPDF'])){
				# mPDF
		        $mPDF1 = Yii::app()->ePdf->mpdf();
		 
		        # You can easily override default constructor's params
		        $mPDF1 = Yii::app()->ePdf->mpdf('', 'A4');
		 
		        # Load a stylesheet
		        //$stylesheet = file_get_contents(Yii::getPathOfAlias('bootstrap.css') . '/main.css');
		        //$mPDF1->WriteHTML($stylesheet, 1);
		 
		        # renderPartial (only 'view' of current controller)
		        $mPDF1->WriteHTML($this->renderPartial('pdf', array('jugadores'=>$jugadores,'model'=>$model, 'torneo' => $torneo), true));
		 
		        # Outputs ready PDF
		        $mPDF1->Output();
			}else{
				$this->render('ListaBuenaFe', array(
					'model'=>$model,
					'jugadores'=>$jugadores,
					'equipo'=>$equipo,
				));
			}
		}else{
			$this->render('ListaBuenaFe', array(
				'model'=>$model,
			));
		}
	}
	
	public function actionSelectEquipos(){
		$idTorneo = $_POST['Equipostorneo']['idTorneo'];
		$criteria = new CDbCriteria;
		$criteria->condition = "idTorneo = :idTorneo";
		$criteria->join = "inner join equipos on t.idEquipo = equipos.idEquipo";
		$criteria->order = "equipos.nombre";
		$criteria->params = array('idTorneo' => $idTorneo);
        $lista = Equipostorneo::model()->findAll($criteria);
        //$lista = CHtml::listData($lista,'id_nivel_dos','descripcion');
        echo CHtml::tag('option', array('value' => ''), 'Seleccione', true);
		
        foreach ($lista as $valor){
        	echo CHtml::tag('option',array('value'=>$valor->idEquipo),$valor->Equipos->Nombre, true );
        	//echo $valor->idEquipo . $valor->Equipos->Nombre;
        }
		//Yii::app()->end();    
		
	}
	
	public function actionGetEquipos(){
		$criteria = new CDbCriteria;
		$criteria->order = 'LOWER(TRIM(Nombre)) ASC';
		$equipos = array();
		foreach(Equipos::model()->findAll($criteria) as $equipo)
		{
			$equipos[] = array(
				'value' => $equipo->idEquipo,
				'text' => $equipo->Nombre,
			);
		}
		echo CJSON::encode($equipos); 
	}
	
    public function actionequiposAutocomplete() {
        $term = trim($_GET['term']);
 
        // Note: Users::usersAutoComplete is the function you created in Step 2
        $equipos = Equipos::equiposAutoComplete($term);
        echo CJSON::encode($equipos);
        Yii::app()->end();
	  }
}
