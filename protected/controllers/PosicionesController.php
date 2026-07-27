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
				'actions'=>array('index','resultados','resumenFecha','fichapartido','verFichaPartido','posicionesJson','resultadosJson','posicionesTorneos'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('guardarResultados','detalleResultado','detalleGoles','detalleTarjetas'),
				'users'=>array('admin','oscarvogel'),
			),
			array('deny',
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
		if((isset($_GET['modo']) && $_GET['modo'] == 'resumen') || isset($_POST['btnResumen']) || isset($_POST['btnPdf'])){
			$this->renderResumenFecha();
			return;
		}

		$model = new Fixture;
		if(isset($_POST['Fixture'])){
			$model->attributes = $_POST['Fixture'];
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

	public function actionResumenFecha(){
		$this->renderResumenFecha();
	}

	private function renderResumenFecha(){
		$model = new Fixture;
		$idTorneo = $this->obtenerTorneoResumen();
		$fecha = date('Y-m-d');

		if(isset($_POST['Fixture'])){
			$model->attributes = $_POST['Fixture'];
			$idTorneo = (int)$_POST['Fixture']['idTorneo'];
			$fecha = $_POST['Fixture']['Fecha'];
		}else{
			$model->idTorneo = $idTorneo;
			$model->Fecha = $fecha;
		}

		$datos = $this->datosResumenFecha($idTorneo, $fecha);

		if(isset($_POST['btnPdf'])){
			$html = $this->renderPartial('resumenFechaPdf', $datos, true);
			$pdf = Yii::app()->ePdf->mpdf('', 'A4');
			$pdf->WriteHTML($html);
			$nombre = 'resumen-' . $idTorneo . '-' . $fecha . '.pdf';
			$pdf->Output($nombre, 'I');
			Yii::app()->end();
		}

		$datos['model'] = $model;
		$this->render('resumenFecha', $datos);
	}

	private function obtenerTorneoResumen(){
		$torneo = Torneo::model()->find(array(
			'condition'=>"Estado='I'",
			'order'=>'idTorneo DESC',
		));
		return $torneo !== null ? $torneo->idTorneo : 1;
	}

	private function datosResumenFecha($idTorneo, $fecha){
		$torneo = Torneo::model()->findByPk($idTorneo);
		$goleadores = $this->topPorCampo(Goles::model()->Goleadores($idTorneo), 'Cantidad', 10);
		$tarjetasAmarillasFechaTodas = Tarjetas::model()->ConsultaTarjetasAmarillas($idTorneo, $fecha);
		$totalAmarillasFecha = $this->sumarCampo($tarjetasAmarillasFechaTodas, 'Amarilla');
		$tarjetasAmarillasTorneoTodas = Tarjetas::model()->ConsultaTarjetasAmarillasTorneo($idTorneo);
		$tarjetasAmarillas = $this->topPorCampo($tarjetasAmarillasTorneoTodas, 'Amarilla', 10);

		return array(
			'idTorneo'=>$idTorneo,
			'fecha'=>$fecha,
			'torneo'=>$torneo,
			'resultados'=>Fixture::model()->ConsultaFecha($idTorneo, $fecha),
			'posiciones'=>Fixture::model()->TablaPosiciones($idTorneo),
			'goleadores'=>$goleadores,
			'tarjetasAmarillas'=>$tarjetasAmarillas,
			'totalAmarillasFecha'=>$totalAmarillasFecha,
		);
	}

	private function topPorCampo($items, $campo, $limite){
		usort($items, function($a, $b) use ($campo){
			$valorA = (int)$a->$campo;
			$valorB = (int)$b->$campo;
			if($valorA === $valorB){
				$nombreA = ($a->Jugador) ? $a->Jugador->Nombre : '';
				$nombreB = ($b->Jugador) ? $b->Jugador->Nombre : '';
				return strcasecmp($nombreA, $nombreB);
			}
			return ($valorA > $valorB) ? -1 : 1;
		});
		return array_slice($items, 0, $limite);
	}

	private function sumarCampo($items, $campo){
		$total = 0;
		foreach($items as $item)
			$total += (int)$item->$campo;
		return $total;
	}

	public function actionGuardarResultados(){
		if(!Yii::app()->request->isPostRequest)
			throw new CHttpException(400,'Peticion no valida.');

		$idTorneo = isset($_POST['idTorneo']) ? (int)$_POST['idTorneo'] : 0;
		$fecha = isset($_POST['Fecha']) ? $_POST['Fecha'] : '';
		$resultados = isset($_POST['Resultado']) && is_array($_POST['Resultado']) ? $_POST['Resultado'] : array();
		$torneo = Torneo::model()->findByPk($idTorneo);
		$actualizados = 0;
		$errores = array();

		foreach($resultados as $idFixture=>$resultado){
			if(!isset($resultado['actualizar']) || !$resultado['actualizar'])
				continue;

			$model = Fixture::model()->findByPk((int)$idFixture);
			if($model===null || (int)$model->idTorneo !== $idTorneo || $model->Fecha !== $fecha){
				$errores[] = 'No se pudo validar el partido ' . (int)$idFixture . '.';
				continue;
			}

			$golLocal = isset($resultado['GolLocal']) ? trim($resultado['GolLocal']) : '';
			$golVisitante = isset($resultado['GolVisitante']) ? trim($resultado['GolVisitante']) : '';
			if($golLocal === '' || $golVisitante === '' || !ctype_digit($golLocal) || !ctype_digit($golVisitante)){
				$errores[] = $this->nombrePartido($model) . ': cargue goles numericos.';
				continue;
			}

			$model->GolLocal = $golLocal;
			$model->GolVisitante = $golVisitante;
			$model->asignaPuntosPorResultado();

			$transaction = Yii::app()->db->beginTransaction();
			if($model->save() && $this->guardarDetallePartido($model, $resultado, $torneo, $errores)){
				$transaction->commit();
				$actualizados++;
			}else{
				$transaction->rollback();
				$errores[] = $this->nombrePartido($model) . ': no se pudo guardar.';
			}
		}

		if($actualizados > 0)
			Yii::app()->user->setFlash('success', $actualizados . ' resultado(s) actualizado(s).');
		if(count($errores) > 0)
			Yii::app()->user->setFlash('error', implode('<br>', $errores));

		$model = new Fixture;
		$model->idTorneo = $idTorneo;
		$model->Fecha = $fecha;
		$datos = Fixture::model()->ConsultaFecha($idTorneo, $fecha);

		$this->render('resultados',array(
			'datos'=>$datos,
			'model'=>$model,
			'torneo'=>$torneo,
		));
	}

	public function actionDetalleResultado($idFixture){
		$fixture = Fixture::model()->findByPk((int)$idFixture);
		if($fixture===null)
			throw new CHttpException(404,'La pagina solicitada no existe.');

		$torneo = Torneo::model()->findByPk($fixture->idTorneo);
		$errores = array();

		if(Yii::app()->request->isPostRequest && isset($_POST['Resultado'])){
			$resultado = $_POST['Resultado'];
			$golLocal = isset($resultado['GolLocal']) ? trim($resultado['GolLocal']) : '';
			$golVisitante = isset($resultado['GolVisitante']) ? trim($resultado['GolVisitante']) : '';

			if($golLocal === '' || $golVisitante === '' || !ctype_digit($golLocal) || !ctype_digit($golVisitante)){
				$errores[] = 'Cargue goles numericos.';
			}else{
				$fixture->GolLocal = $golLocal;
				$fixture->GolVisitante = $golVisitante;
				$fixture->asignaPuntosPorResultado();

				$transaction = Yii::app()->db->beginTransaction();
				if($fixture->save() && $this->guardarDetallePartido($fixture, $resultado, $torneo, $errores)){
					$transaction->commit();
					Yii::app()->user->setFlash('success', 'Resultado, goleadores y tarjetas guardados.');
					$this->refresh();
				}else{
					$transaction->rollback();
					if(count($errores) === 0)
						$errores[] = 'No se pudo guardar el detalle del partido.';
				}
			}

			if(count($errores) > 0)
				Yii::app()->user->setFlash('error', implode('<br>', $errores));
		}

		$this->render('detalleResultado', array(
			'fixture'=>$fixture,
			'torneo'=>$torneo,
		));
	}

	public function actionDetalleGoles($idFixture){
		$fixture = Fixture::model()->findByPk((int)$idFixture);
		if($fixture===null)
			throw new CHttpException(404,'La pagina solicitada no existe.');

		$torneo = Torneo::model()->findByPk($fixture->idTorneo);
		$errores = array();

		if(Yii::app()->request->isPostRequest && isset($_POST['Resultado'])){
			$resultado = $_POST['Resultado'];
			if($this->guardarGolesPartido($fixture, $resultado, $torneo, $errores))
				Yii::app()->user->setFlash('success', 'Goleadores guardados.');
			else
				Yii::app()->user->setFlash('error', implode('<br>', $errores));
		}

		$this->renderPartial('_detalleGoles', array(
			'fixture'=>$fixture,
			'torneo'=>$torneo,
		), false, true);
	}

	public function actionDetalleTarjetas($idFixture){
		$fixture = Fixture::model()->findByPk((int)$idFixture);
		if($fixture===null)
			throw new CHttpException(404,'La pagina solicitada no existe.');

		$torneo = Torneo::model()->findByPk($fixture->idTorneo);
		$errores = array();

		if(Yii::app()->request->isPostRequest && isset($_POST['Resultado'])){
			$resultado = $_POST['Resultado'];
			if($this->guardarTarjetasPartido($fixture, $resultado, $torneo, $errores))
				Yii::app()->user->setFlash('success', 'Tarjetas guardadas.');
			else
				Yii::app()->user->setFlash('error', implode('<br>', $errores));
		}

		$this->renderPartial('_detalleTarjetas', array(
			'fixture'=>$fixture,
			'torneo'=>$torneo,
		), false, true);
	}

	private function guardarDetallePartido($fixture, $resultado, $torneo, &$errores){
		if(isset($resultado['Goles']) && is_array($resultado['Goles']) && !$this->guardarGolesPartido($fixture, $resultado, $torneo, $errores))
			return false;

		if(isset($resultado['Tarjetas']) && is_array($resultado['Tarjetas']) && !$this->guardarTarjetasPartido($fixture, $resultado, $torneo, $errores))
			return false;

		return true;
	}

	private function guardarGolesPartido($fixture, $resultado, $torneo, &$errores){
		if(!isset($resultado['Goles']) || !is_array($resultado['Goles']))
			return true;

		$transaction = Yii::app()->db->getCurrentTransaction() === null ? Yii::app()->db->beginTransaction() : null;
		Goles::model()->deleteAllByAttributes(array('idFixture'=>$fixture->idFixture));

		foreach($resultado['Goles'] as $golData){
			$idJugador = isset($golData['idJugador']) ? (int)$golData['idJugador'] : 0;
			$cantidad = isset($golData['Cantidad']) ? trim($golData['Cantidad']) : '';
			if($idJugador === 0 && $cantidad === '')
				continue;
			if($idJugador === 0 || $cantidad === '' || !ctype_digit($cantidad) || (int)$cantidad <= 0){
				$errores[] = $this->nombrePartido($fixture) . ': revise los goleadores.';
				if($transaction !== null) $transaction->rollback();
				return false;
			}
			if(!$this->jugadorPerteneceAlPartido($idJugador, $fixture, $torneo)){
				$errores[] = $this->nombrePartido($fixture) . ': un goleador no pertenece al partido.';
				if($transaction !== null) $transaction->rollback();
				return false;
			}

			$gol = new Goles;
			$gol->idFixture = $fixture->idFixture;
			$gol->idJugador = $idJugador;
			$gol->Cantidad = (int)$cantidad;
			if(!$gol->save()){
				if($transaction !== null) $transaction->rollback();
				return false;
			}
		}

		if($transaction !== null) $transaction->commit();
		return true;
	}

	private function guardarTarjetasPartido($fixture, $resultado, $torneo, &$errores){
		if(!isset($resultado['Tarjetas']) || !is_array($resultado['Tarjetas']))
			return true;

		$transaction = Yii::app()->db->getCurrentTransaction() === null ? Yii::app()->db->beginTransaction() : null;
		Tarjetas::model()->deleteAllByAttributes(array('idFixture'=>$fixture->idFixture));

		foreach($resultado['Tarjetas'] as $tarjetaData){
			$idJugador = isset($tarjetaData['idJugador']) ? (int)$tarjetaData['idJugador'] : 0;
			$amarilla = isset($tarjetaData['Amarilla']) ? 1 : 0;
			$roja = isset($tarjetaData['Roja']) ? 1 : 0;
			$desdeFecha = isset($tarjetaData['DesdeFecha']) ? trim($tarjetaData['DesdeFecha']) : '';
			$hastaFecha = isset($tarjetaData['HastaFecha']) ? trim($tarjetaData['HastaFecha']) : '';
			$motivo = isset($tarjetaData['Motivo']) ? trim($tarjetaData['Motivo']) : '';
			if($idJugador === 0 && !$amarilla && !$roja && $desdeFecha === '' && $hastaFecha === '' && $motivo === '')
				continue;
			if($idJugador === 0 || (!$amarilla && !$roja)){
				$errores[] = $this->nombrePartido($fixture) . ': revise las tarjetas.';
				if($transaction !== null) $transaction->rollback();
				return false;
			}
			if(!$this->jugadorPerteneceAlPartido($idJugador, $fixture, $torneo)){
				$errores[] = $this->nombrePartido($fixture) . ': un jugador con tarjeta no pertenece al partido.';
				if($transaction !== null) $transaction->rollback();
				return false;
			}

			$tarjeta = new Tarjetas;
			$tarjeta->idFixture = $fixture->idFixture;
			$tarjeta->idJugador = $idJugador;
			$tarjeta->idEquipo = $this->equipoJugadorEnPartido($idJugador, $fixture, $torneo);
			$tarjeta->Amarilla = $amarilla;
			$tarjeta->Roja = $roja;
			$tarjeta->DesdeFecha = $desdeFecha;
			$tarjeta->HastaFecha = $hastaFecha === '' ? null : $hastaFecha;
			$tarjeta->Motivo = $motivo;
			if(!$tarjeta->save()){
				if($transaction !== null) $transaction->rollback();
				return false;
			}
		}

		if($transaction !== null) $transaction->commit();
		return true;
	}

	private function jugadorPerteneceAlPartido($idJugador, $fixture, $torneo){
		return $this->equipoJugadorEnPartido($idJugador, $fixture, $torneo) !== null;
	}

	private function equipoJugadorEnPartido($idJugador, $fixture, $torneo){
		$jugador = Jugador::model()->findByPk($idJugador);
		if($jugador !== null && ((int)$jugador->idEquipo === (int)$fixture->Local || (int)$jugador->idEquipo === (int)$fixture->Visitante))
			return $jugador->idEquipo;

		if($torneo !== null && $torneo->Estado == 'F'){
			$criteria = new CDbCriteria;
			$criteria->condition = 'idJugador=:idJugador and idTorneo=:idTorneo and idEquipo in (:local,:visitante)';
			$criteria->params = array(
				':idJugador'=>$idJugador,
				':idTorneo'=>$fixture->idTorneo,
				':local'=>$fixture->Local,
				':visitante'=>$fixture->Visitante,
			);
			$historico = Historicojugador::model()->find($criteria);
			if($historico !== null)
				return $historico->idEquipo;
		}

		return null;
	}

	private function nombrePartido($fixture){
		$local = $fixture->local !== null ? $fixture->local->Nombre : 'Libre';
		$visitante = $fixture->visitante !== null ? $fixture->visitante->Nombre : 'Libre';
		return $local . ' - ' . $visitante;
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
