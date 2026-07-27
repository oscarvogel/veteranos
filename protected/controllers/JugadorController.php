<?php

class JugadorController extends Controller
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
				'actions'=>array('index','view','Actualiza','historia','jugadorAutocomplete'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update', 'liberar', 'importarListaBuenaFe', 'documentacion', 'legajo', 'subirDocumento', 'descargarDocumento', 'verDocumento', 'eliminarDocumento'),
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
	public function actionCreate()
	{
		$model=new Jugador;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Jugador']))
		{
			$model->attributes=$_POST['Jugador'];
			if($model->save())
				$this->redirect(array('admin'));
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

		if(isset($_POST['Jugador']))
		{
			$model->attributes=$_POST['Jugador'];
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
		$dataProvider=new CActiveDataProvider('Jugador');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Jugador('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Jugador']))
			$model->attributes=$_GET['Jugador'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionImportarListaBuenaFe()
	{
		$resultado = null;
		$error = null;
		$idEquipo = isset($_POST['idEquipo']) ? (int)$_POST['idEquipo'] : '';

		if(Yii::app()->request->isPostRequest)
		{
			$archivo = CUploadedFile::getInstanceByName('archivo');
			if($archivo === null)
			{
				$error = 'Debe subir un archivo CSV o XLSX.';
			}
			else
			{
				try
				{
					$importador = new ListaBuenaFeImporter();
					$resultado = $importador->importarArchivo($archivo->tempName, $archivo->name, $idEquipo);
					Yii::app()->user->setFlash('success', 'Importacion finalizada.');
				}
				catch(Exception $e)
				{
					$error = $e->getMessage();
				}
			}
		}

		$this->render('importarListaBuenaFe', array(
			'idEquipo' => $idEquipo,
			'resultado' => $resultado,
			'error' => $error,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionDocumentacion()
	{
		$jugadores = new Jugador;
		$resultados = array();
		$termino = isset($_GET['q']) ? trim($_GET['q']) : '';
		$dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';
		$idEquipo = isset($_GET['idEquipo']) ? trim($_GET['idEquipo']) : '';
		$soloFaltantes = isset($_GET['faltantes']) && (int)$_GET['faltantes'] === 1;
		$tipoFaltante = isset($_GET['tipoFaltante']) ? trim($_GET['tipoFaltante']) : '';

		if(isset($_GET['idJugador']) && (int)$_GET['idJugador'] > 0)
			$this->redirect(array('legajo', 'id'=>(int)$_GET['idJugador']));

		if(Yii::app()->request->isPostRequest && isset($_POST['Jugador'])) {
			$idJugador = isset($_POST['Jugador']['idJugador']) ? (int)$_POST['Jugador']['idJugador'] : 0;
			if($idJugador > 0)
				$this->redirect(array('legajo', 'id'=>$idJugador));

			Yii::app()->user->setFlash('error', 'Seleccione un jugador de la lista para abrir su legajo.');
			$jugadores->attributes = $_POST['Jugador'];
		}

		$hayBusqueda = $termino !== '' || $dni !== '' || $idEquipo !== '' || $soloFaltantes;
		if($hayBusqueda) {
			$criteria = new CDbCriteria;
			$criteria->with = array('Equipo');
			$criteria->order = 't.Nombre ASC';
			$criteria->limit = $soloFaltantes ? 200 : 50;

			if($termino !== '')
				$criteria->compare('t.Nombre', $termino, true);
			if($dni !== '')
				$criteria->compare('t.DNI', $dni, true);
			if($idEquipo !== '')
				$criteria->compare('t.idEquipo', $idEquipo);
			if($soloFaltantes)
				$this->aplicarFiltroDocumentacionFaltante($criteria, $tipoFaltante);

			$resultados = Jugador::model()->findAll($criteria);
		}

		$this->render('documentacion',array(
			'jugadores'=>$jugadores,
			'resultados'=>$resultados,
			'hayBusqueda'=>$hayBusqueda,
			'termino'=>$termino,
			'dni'=>$dni,
			'idEquipo'=>$idEquipo,
			'soloFaltantes'=>$soloFaltantes,
			'tipoFaltante'=>$tipoFaltante,
		));
	}

	public function actionLegajo($id)
	{
		$jugador = $this->loadModel($id);
		$this->asegurarAccesoJugadorLegajo($jugador);

		$documentos = JugadorDocumento::model()->findAllByAttributes(
			array('idJugador'=>$jugador->idJugador),
			array('order'=>'created_at DESC, idDocumento DESC')
		);
		$foto = $jugador->getFotoLegajo();

		$this->render('legajo', array(
			'jugador'=>$jugador,
			'documentos'=>$documentos,
			'documento'=>new JugadorDocumento,
			'foto'=>$foto,
			'puedeEliminar'=>$this->puedeEliminarDocumentosJugador($jugador),
		));
	}

	public function actionSubirDocumento($id)
	{
		if(!Yii::app()->request->isPostRequest)
			throw new CHttpException(400, 'Peticion no valida.');

		$jugador = $this->loadModel($id);
		$this->asegurarAccesoJugadorLegajo($jugador);
		$esCargaRapida = isset($_POST['fotoRapida']) && (int)$_POST['fotoRapida'] === 1;
		$redirectLegajo = array('legajo', 'id'=>$jugador->idJugador);

		$archivo = CUploadedFile::getInstanceByName('archivo');
		$tipo = isset($_POST['JugadorDocumento']['tipo']) ? trim($_POST['JugadorDocumento']['tipo']) : '';
		$titulo = isset($_POST['JugadorDocumento']['titulo']) ? trim($_POST['JugadorDocumento']['titulo']) : '';
		$observacion = isset($_POST['JugadorDocumento']['observacion']) ? trim($_POST['JugadorDocumento']['observacion']) : '';

		$errores = array();
		if($archivo === null)
			$errores[] = 'Debe seleccionar un archivo.';
		$tipos = JugadorDocumento::getTipos();
		if(!isset($tipos[$tipo]))
			$errores[] = 'Debe seleccionar un tipo de documento valido.';

		if($archivo !== null) {
			$extension = strtolower($archivo->extensionName);
			$mime = JugadorDocumento::detectarMime($archivo->tempName, $archivo->type);
			if(!JugadorDocumento::esExtensionPermitida($extension))
				$errores[] = 'Solo se permiten archivos PDF, JPG, JPEG o PNG.';
			if(!JugadorDocumento::esMimePermitido($mime))
				$errores[] = 'El contenido del archivo no coincide con un PDF o imagen permitida.';
			if(JugadorDocumento::esTipoImagen($tipo) && !JugadorDocumento::esExtensionImagenPermitida($extension))
				$errores[] = 'La foto del jugador debe ser JPG, JPEG o PNG.';
			if(JugadorDocumento::esTipoImagen($tipo) && !JugadorDocumento::esMimeImagenPermitido($mime))
				$errores[] = 'La foto del jugador debe ser una imagen valida JPG o PNG.';
			if((int)$archivo->size > JugadorDocumento::MAX_FILE_SIZE)
				$errores[] = 'El archivo no puede superar los 10 MB.';
		}

		if(!empty($errores)) {
			$this->responderSubidaDocumento($esCargaRapida, false, implode('<br>', $errores), $redirectLegajo);
		}

		$documento = new JugadorDocumento;
		$documento->idJugador = $jugador->idJugador;
		$documento->tipo = $tipo;
		$documento->titulo = $titulo !== '' ? $titulo : (JugadorDocumento::esTipoImagen($tipo) ? 'Foto jugador' : '');
		$documento->observacion = $observacion;
		$documento->archivo_original = $archivo->name;
		$documento->archivo_guardado = JugadorDocumento::generarNombreGuardado($jugador->idJugador, $tipo, $archivo->name);
		$documento->mime_type = JugadorDocumento::detectarMime($archivo->tempName, $archivo->type);
		$documento->extension = strtolower($archivo->extensionName);
		$documento->tamano_bytes = (int)$archivo->size;
		$documento->idUsuario = Yii::app()->user->isGuest ? null : Yii::app()->user->getId();

		if(!$documento->validate()) {
			$this->responderSubidaDocumento($esCargaRapida, false, CHtml::errorSummary($documento), $redirectLegajo);
		}

		$directorio = JugadorDocumento::asegurarDirectorioJugador($jugador->idJugador);
		$destino = $directorio . DIRECTORY_SEPARATOR . $documento->archivo_guardado;
		if(!$archivo->saveAs($destino)) {
			$this->responderSubidaDocumento($esCargaRapida, false, 'No se pudo guardar el archivo en el legajo.', $redirectLegajo);
		}

		if(!$documento->save(false)) {
			if(is_file($destino))
				@unlink($destino);
			$this->responderSubidaDocumento($esCargaRapida, false, 'No se pudo registrar el documento en la base.', $redirectLegajo);
		}

		$this->responderSubidaDocumento($esCargaRapida, true, 'Documento cargado correctamente.', $redirectLegajo);
	}

	private function responderSubidaDocumento($esCargaRapida, $ok, $mensaje, $redirect)
	{
		if($esCargaRapida) {
			$mensajeJson = str_replace(array('<br>', '<br/>', '<br />'), "\n", $mensaje);
			$mensajeJson = trim(strip_tags($mensajeJson));
			header('Content-Type: application/json; charset=utf-8');
			echo CJSON::encode(array(
				'ok'=>$ok ? true : false,
				'message'=>$mensajeJson,
			));
			Yii::app()->end();
		}

		Yii::app()->user->setFlash($ok ? 'success' : 'error', $mensaje);
		$this->redirect($redirect);
	}

	public function actionDescargarDocumento($idDocumento)
	{
		$documento = $this->loadDocumentoLegajo($idDocumento);
		$this->asegurarAccesoDocumentoLegajo($documento);

		$path = $documento->getAbsolutePath();
		if(!is_file($path))
			throw new CHttpException(404, 'El archivo solicitado no existe en el legajo.');

		Yii::app()->request->sendFile($documento->archivo_original, file_get_contents($path), $documento->mime_type);
	}

	public function actionVerDocumento($idDocumento)
	{
		$documento = $this->loadDocumentoLegajo($idDocumento);
		$this->asegurarAccesoDocumentoLegajo($documento);

		if(!JugadorDocumento::esMimeImagenPermitido($documento->mime_type))
			throw new CHttpException(404, 'El archivo solicitado no es una imagen.');

		$path = $documento->getAbsolutePath();
		if(!is_file($path))
			throw new CHttpException(404, 'El archivo solicitado no existe en el legajo.');

		header('Content-Type: ' . $documento->mime_type);
		header('Content-Length: ' . filesize($path));
		header('Cache-Control: private, max-age=3600');
		readfile($path);
		Yii::app()->end();
	}

	public function actionEliminarDocumento($idDocumento)
	{
		if(!Yii::app()->request->isPostRequest)
			throw new CHttpException(400, 'Peticion no valida.');

		$documento = $this->loadDocumentoLegajo($idDocumento);
		$jugadorId = $documento->idJugador;
		$this->asegurarAccesoDocumentoLegajo($documento, true);

		if($documento->delete())
			Yii::app()->user->setFlash('success', 'Documento eliminado.');
		else
			Yii::app()->user->setFlash('error', 'No se pudo eliminar el documento.');

		$this->redirect(array('legajo', 'id'=>$jugadorId));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Jugador::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'La página solicitada no existe. ID buscado ' . $id);
		return $model;
	}

	private function loadDocumentoLegajo($idDocumento)
	{
		$model = JugadorDocumento::model()->findByPk($idDocumento);
		if($model === null)
			throw new CHttpException(404, 'El documento solicitado no existe.');

		return $model;
	}

	private function aplicarFiltroDocumentacionFaltante($criteria, $tipoFaltante)
	{
		$tiposValidos = array_merge(JugadorDocumento::getTiposDocumentacionRequerida(), array(JugadorDocumento::TIPO_FOTO));
		if($tipoFaltante !== '' && !in_array($tipoFaltante, $tiposValidos, true))
			$tipoFaltante = '';

		$conditions = array();
		$params = array();
		$tipos = $tipoFaltante !== '' ? array($tipoFaltante) : $tiposValidos;

		foreach($tipos as $tipo) {
			if($tipo === JugadorDocumento::TIPO_FOTO) {
				$conditions[] = 'NOT EXISTS (SELECT 1 FROM jugador_documento jd_foto WHERE jd_foto.idJugador = t.idJugador AND jd_foto.tipo = :tipoFoto)';
				$params[':tipoFoto'] = JugadorDocumento::TIPO_FOTO;
				continue;
			}

			$campo = JugadorDocumento::getCampoLegacyPorTipo($tipo);
			if($campo !== null)
				$conditions[] = '(t.' . $campo . ' IS NULL OR t.' . $campo . ' <> 1)';
		}

		if(!empty($conditions))
			$criteria->addCondition('(' . implode(' OR ', $conditions) . ')');
		foreach($params as $name=>$value)
			$criteria->params[$name] = $value;
	}

	private function asegurarAccesoDocumentoLegajo($documento, $eliminar = false)
	{
		$jugador = $documento->Jugador;
		if($jugador === null)
			throw new CHttpException(404, 'El jugador del documento no existe.');

		$this->asegurarAccesoJugadorLegajo($jugador);
		if($eliminar && !$this->puedeEliminarDocumentosJugador($jugador))
			throw new CHttpException(403, 'No tiene permiso para eliminar documentos de este legajo.');
	}

	private function asegurarAccesoJugadorLegajo($jugador)
	{
		if($this->esUsuarioInternoLegajo())
			return;

		$equipo = $this->getEquipoUsuarioActual();
		if($equipo !== null && (int)$jugador->idEquipo === (int)$equipo->idEquipo)
			return;

		throw new CHttpException(403, 'No tiene permiso para acceder al legajo de este jugador.');
	}

	private function puedeEliminarDocumentosJugador($jugador)
	{
		return $this->esUsuarioInternoLegajo() || $this->jugadorPerteneceAlEquipoActual($jugador);
	}

	private function jugadorPerteneceAlEquipoActual($jugador)
	{
		$equipo = $this->getEquipoUsuarioActual();
		return $equipo !== null && (int)$jugador->idEquipo === (int)$equipo->idEquipo;
	}

	private function esUsuarioInternoLegajo()
	{
		if(Yii::app()->user->isGuest)
			return false;

		foreach(array('action_jugador_admin', 'action_jugador_update', 'action_jugador_delete') as $operation) {
			if(Yii::app()->user->checkAccess($operation))
				return true;
		}

		return in_array(Yii::app()->user->name, array('admin', 'oscarvogel'), true);
	}

	private function getEquipoUsuarioActual()
	{
		if(Yii::app()->user->isGuest)
			return null;

		return Equipos::model()->findByAttributes(array('idUsuario'=>Yii::app()->user->getId()));
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='jugador-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionActualiza(){
		$model = $this->loadModel($_POST['pk']);
		$campo = null;

		if ($_POST['name'] == 'idEquipo') {
			$model->idEquipo = $_POST['value'];
			$campo = 'idEquipo';
		} elseif ($_POST['name'] == 'Observacion') {
			$model->Observacion = $_POST['value'];
			$campo = 'Observacion';
		} elseif ($_POST['name'] == 'Nombre') {
			$model->Nombre = $_POST['value'];
			$campo = 'Nombre';
		} elseif ($_POST['name'] == 'Clase') {
			$model->Clase = $_POST['value'];
			$campo = 'Clase';
		} elseif ($_POST['name'] == 'DNI') {
			$model->DNI = $_POST['value'];
			$campo = 'DNI';
		} elseif ($_POST['name'] == 'fecha_nacimiento') {
			$fecha_str = $_POST['value'];
			$campo = 'fecha_nacimiento';
			if (empty($fecha_str)) {
				$model->fecha_nacimiento = null;
			} else {
				$fecha_str = trim($fecha_str);
				if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha_str, $matches)) {
					$dia = $matches[1];
					$mes = $matches[2];
					$ano = $matches[3];
					$model->fecha_nacimiento = $ano . '-' . $mes . '-' . $dia;
					$model->Clase = $ano;
				} elseif (preg_match('/^(\d{2})(\d{2})(\d{4})$/', $fecha_str, $matches)) {
					$dia = $matches[1];
					$mes = $matches[2];
					$ano = $matches[3];
					$model->fecha_nacimiento = $ano . '-' . $mes . '-' . $dia;
					$model->Clase = $ano;
				}
			}
		} elseif ($_POST['name'] == 'certificado') {
			$model->DNI = $_POST['value'];
			$campo = 'DNI';
		} elseif ($_POST['name'] == 'firma_lista') {
			$model->DNI = $_POST['value'];
			$campo = 'DNI';
		} elseif ($_POST['name'] == 'fotocopia_dni') {
			$model->DNI = $_POST['value'];
			$campo = 'DNI';
		} elseif ($_POST['name'] == 'dec_jurada') {
			$model->DNI = $_POST['value'];
			$campo = 'DNI';
		} elseif ($_POST['name'] == 'es_socio') {
			$model->es_socio = (int)$_POST['value'] === 1 ? 1 : 0;
			$campo = 'es_socio';
		}

		// Validar solo el campo editado
		if ($campo && $model->validate(array($campo))) {
			if ($model->save(false)) {
				echo '';
				Yii::app()->end();
			} else {
				$msg = 'Error al guardar en la base de datos.';
			}
		} else {
			$msg = CHtml::errorSummary($model);
		}

		header('HTTP/1.1 400 Bad Request');
		echo $msg;
		Yii::app()->end();
	}
	
	public function actionHistoria($idJugador = 0){
		$jugadores = new Jugador;
		if($idJugador != 0 or isset($_POST['Jugador'])){
			$idJugador 	= $_POST['Jugador']['idJugador'];
			$jugador	= $this->loadModel($idJugador);
			$equipojug	= Historicojugador::model()->ConsultaEquiposJugador($idJugador);
			$amarillas 	= Tarjetas::model()->ConsultaTarjetasJugador($idJugador, 1);
			$rojas 		= Tarjetas::model()->ConsultaTarjetasJugador($idJugador, 0);
			$goles		= Goles::model()->golJugador($idJugador);
			
			$this->render('historia',array(
				'jugadores' => $jugadores,
				'amarillas' => $amarillas,
				'rojas' 	=> $rojas,
				'goles' 	=> $goles,
				'jugador'	=> $jugador,
				'equipojug'	=> $equipojug
			));
		}else{
			$this->render('historia',array(
				'jugadores' => $jugadores
			));
		}
	}
	
	 public function actionjugadorAutocomplete() {
        	$term = trim($_GET['term']) ;
 
            // Note: Users::usersAutoComplete is the function you created in Step 2
      		$jugadores =  Jugador::jugadorAutoComplete($term);
            echo CJSON::encode($jugadores);
            Yii::app()->end();
	  }
	 
	 public function actionLiberar(){
	 	$model = new Torneo;
		
		if(isset($_POST['Torneo'])){
			$Tarjetas = Jugador::model()->Liberar($_POST['Torneo']['idTorneo']);
			$this->render('liberar',array(
				'model' => $model,
			));
		}else{
			$this->render('liberar',array(
				'model' => $model,
			));
		}
		
	 }
    
    public function actionAsignaequipo(){
        $jugadores = new Jugador;
        if(isset($_POST['btnAsignar'])){
            $idJugador = $this->getSelectedJugadorId($jugadores);
            if($idJugador === null){
                $this->render('asignajugador', array(
                    'jugadores'=>$jugadores,
                ));
                return;
            }
            $jugador	= $this->loadModel($idJugador);
            $equipo = Equipos::model()->find('idUsuario='.Yii::app()->user->getId());
            $camposGuardar = array('idEquipo');

            if(trim((string)$jugador->fecha_nacimiento) === ''){
                $fechaNacimiento = isset($_POST['Jugador']['fecha_nacimiento']) ? trim($_POST['Jugador']['fecha_nacimiento']) : '';
                if($fechaNacimiento === ''){
                    $jugador->addError('fecha_nacimiento', 'Debe cargar la fecha de nacimiento para asignar el jugador. Formato: dd/mm/aaaa.');
                    $jugadores->idJugador = $idJugador;
                    $jugadores->Nombre = $jugador->Nombre;
                    $this->render('asignajugador', array(
                        'jugadores'=>$jugadores,
                        'jugador'=>$jugador,
                        'equipo'=>$equipo,
                        'mostrarModalFechaNacimiento'=>true,
                    ));
                    return;
                }

                if(!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fechaNacimiento, $matches)
                    || !checkdate((int)$matches[2], (int)$matches[1], (int)$matches[3])){
                    $jugador->addError('fecha_nacimiento', 'La fecha de nacimiento debe tener el formato dd/mm/aaaa y ser una fecha valida.');
                    $jugadores->idJugador = $idJugador;
                    $jugadores->Nombre = $jugador->Nombre;
                    $this->render('asignajugador', array(
                        'jugadores'=>$jugadores,
                        'jugador'=>$jugador,
                        'equipo'=>$equipo,
                        'mostrarModalFechaNacimiento'=>true,
                    ));
                    return;
                }

                $jugador->fecha_nacimiento = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                $jugador->Clase = $matches[3];
                $camposGuardar = array('idEquipo', 'fecha_nacimiento', 'Clase');
            }

            $jugador->idEquipo = $equipo->idEquipo;
            if(!$jugador->save(false, $camposGuardar)){
                throw new CHttpException(400,'Petición no Válida. Por favor, no repita esta solicitud de nuevo.');
            }
            $this->render('asignajugador', array(
                'jugadores'=>$jugadores,
            ));
        }elseif(isset($_POST['btnBorrar'])){
            $idJugador = $this->getSelectedJugadorId($jugadores);
            if($idJugador === null){
                $this->render('asignajugador', array(
                    'jugadores'=>$jugadores,
                ));
                return;
            }
            $jugador	= $this->loadModel($idJugador);
            $jugador->idEquipo = 0;
            if(!$jugador->save(false, array('idEquipo'))){
                throw new CHttpException(400,'Petición no Válida. Por favor, no repita esta solicitud de nuevo.');
            }
            $this->render('asignajugador', array(
                'jugadores'=>$jugadores,
            ));
        }elseif(isset($_POST['Jugador'])){
            $idJugador = $this->getSelectedJugadorId($jugadores);
            if($idJugador === null){
                $this->render('asignajugador', array(
                    'jugadores'=>$jugadores,
                ));
                return;
            }
            $jugador	= $this->loadModel($idJugador);
            $equipo = Equipos::model()->find('idUsuario='.Yii::app()->user->getId());
            $jugadores->idJugador=$idJugador;
            $jugadores->Nombre=$jugador->Nombre;
            $this->render('asignajugador', array(
                'jugadores'=>$jugadores,
                'jugador'=>$jugador,
                'equipo'=>$equipo,
            ));
        
        }else{
            $this->render('asignajugador', array(
                'jugadores'=>$jugadores,
            ));
        }
    }

    private function getSelectedJugadorId($jugadores){
        $idJugador = isset($_POST['Jugador']['idJugador']) ? trim((string)$_POST['Jugador']['idJugador']) : '';
        if($idJugador === '' || !ctype_digit($idJugador)){
            $jugadores->addError('Nombre', 'Seleccione un jugador de la lista de resultados.');
            return null;
        }

        return $idJugador;
    }
    
    public function actionListajugadores(){
        $equipo = Equipos::model()->find('idUsuario='.Yii::app()->user->getId());
        $criteria = new CDbCriteria();
		$criteria->condition = "idEquipo=:idEquipo";
		$criteria->params = array('idEquipo'=>$equipo->idEquipo);
        $jugadores = Jugador::model()->findAll($criteria);
        $this->render('listajugadores', array(
            'jugadores'=>$jugadores,
        ));
    }

    
    public function actionJugadorequipo(){
        $equipos = new Equipos;
        if(isset($_POST['btnConsultar'])){
            $criteria = new CDbCriteria();
            $criteria->condition = "idEquipo=:idEquipo";
            $criteria->params = array('idEquipo'=>$_POST['Equipos']['idEquipo']);
            $criteria->order = "nombre";
            $jugadores = Jugador::model()->findAll($criteria);
            $this->render('jugadoresequipo', array(
                'equipos'=>$equipos,'jugadores'=>$jugadores
            ));
        }else{
            $this->render('jugadoresequipo', array(
                'equipos'=>$equipos
        ));

        }
    }
}

