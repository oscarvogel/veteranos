<?php

/**
 * Controller del prode.
 *
 * Acciones publicas:  index, register, login, logout, ranking, resultados
 * Acciones con login: panel, predict, miRanking
 * Acciones admin:    admin, loadResultados, publicar, recalcular, usuarios
 */
class ProdeController extends Controller
{
	/**
	 * Filtros: si la accion requiere admin, se chequea ProdeSession::requireAdmin().
	 * Lo manejamos en cada accion puntual para no atar todo el controller.
	 */
	public $layout = '//layouts/column1';

	// ============================================================
	// PUBLICAS
	// ============================================================

	public function actionIndex()
	{
		$user = ProdeSession::user();
		if ($user !== null) {
			$this->redirect(array('prode/panel'));
		}
		$this->pageTitle = 'Prode Veteranos - ' . Yii::app()->name;
		$this->render('index');
	}

	public function actionRegister()
	{
		$user = ProdeSession::user();
		if ($user !== null) {
			$this->redirect(array('prode/panel'));
		}

		$model = new ProdeUsuario;
		$model->scenario = 'register';

		if (isset($_POST['ProdeUsuario'])) {
			$model->attributes = $_POST['ProdeUsuario'];
			$model->activo = 1;
			$model->esAdmin = 0;
			// idEquipo: si viene 0 o vacio, queda NULL (Sin equipo)
			$idEq = isset($_POST['ProdeUsuario']['idEquipo']) ? (int)$_POST['ProdeUsuario']['idEquipo'] : 0;
			$model->idEquipo = $idEq > 0 ? $idEq : null;
			$model->setPassword($_POST['ProdeUsuario']['password']);
			if ($model->save()) {
				ProdeSession::login($model);
				Yii::app()->user->setFlash('prode_ok', 'Bienvenido al prode, ' . $model->nombre . '!');
				$this->redirect(array('prode/panel'));
			}
		}

		$this->pageTitle = 'Registrarse - Prode';
		$this->render('register', array('model' => $model));
	}

	public function actionLogin()
	{
		$user = ProdeSession::user();
		if ($user !== null) {
			$this->redirect(array('prode/panel'));
		}

		$email = '';
		$error = null;
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$email = trim((string)$_POST['email']);
			$password = (string)$_POST['password'];
			$identity = new ProdeUserIdentity($email, $password);
			if ($identity->authenticate()) {
				ProdeSession::login($identity->getUser());
				$this->redirect(array('prode/panel'));
			}
			$error = $identity->errorCode === ProdeUserIdentity::ERROR_USERNAME_INVALID
				? 'Email no registrado.'
				: ($identity->errorCode === ProdeUserIdentity::ERROR_PASSWORD_INVALID
					? 'Contrasena incorrecta.'
					: 'No se pudo iniciar sesion.');
		}

		$this->pageTitle = 'Iniciar sesion - Prode';
		$this->render('login', array('email' => $email, 'error' => $error));
	}

	public function actionLogout()
	{
		ProdeSession::logout();
		$this->redirect(array('prode/index'));
	}

	public function actionRanking()
	{
		$this->pageTitle = 'Ranking - Prode';

		$criteria = new CDbCriteria;
		$criteria->condition = "t.activo = 1";
		$criteria->order = 't.nombre ASC';
		$usuarios = ProdeUsuario::model()->findAll($criteria);

		$rows = array();
		foreach ($usuarios as $u) {
			$rows[] = array(
				'usuario' => $u,
				'puntos' => $u->totalPuntos(),
				'exactos' => $u->countExactos(),
			);
		}
		// Orden: puntos desc, exactos desc, nombre asc
		usort($rows, function($a, $b) {
			if ($a['puntos'] !== $b['puntos']) return $b['puntos'] - $a['puntos'];
			if ($a['exactos'] !== $b['exactos']) return $b['exactos'] - $a['exactos'];
			return strcasecmp($a['usuario']->nombre, $b['usuario']->nombre);
		});

		$this->render('ranking', array('rows' => $rows));
	}

	public function actionResultados($idTorneo, $fecha)
	{
		$idTorneo = (int)$idTorneo;
		$fecha = (int)$fecha;

		$torneo = Torneo::model()->findByPk($idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'Torneo no encontrado.');
		}
		if (!ProdeFechaPublicada::estaPublicada($idTorneo, $fecha)) {
			throw new CHttpException(404, 'Esta fecha aun no fue publicada.');
		}

		$partidos = Fixture::model()->ConsultaFixture($idTorneo);
		$partidosFecha = array();
		foreach ($partidos as $p) {
			if ((int)$p->NFecha === $fecha) {
				$partidosFecha[] = $p;
			}
		}

		// predicciones agrupadas por partido
		$predicciones = array();
		if (!empty($partidosFecha)) {
			$idsFixture = array();
			foreach ($partidosFecha as $p) $idsFixture[] = (int)$p->idFixture;
			$criteria = new CDbCriteria;
			$criteria->addInCondition('idFixture', $idsFixture);
			$todas = ProdePrediccion::model()->findAll($criteria);
			foreach ($todas as $pred) {
				$predicciones[(int)$pred->idFixture][] = $pred;
			}
		}

		$this->pageTitle = 'Fecha ' . $fecha . ' - ' . $torneo->Nombre;
		$this->render('resultados', array(
			'torneo' => $torneo,
			'fecha' => $fecha,
			'partidos' => $partidosFecha,
			'predicciones' => $predicciones,
		));
	}

	public function actionRankingEquipos()
	{
		$this->pageTitle = 'Ranking por equipos - Prode';
		$rows = ProdeUsuario::getEquiposConPuntos();
		$this->render('ranking_equipos', array('rows' => $rows));
	}

	public function actionEquipo($idEquipo)
	{
		$idEquipo = (int)$idEquipo;
		$equipo = Equipos::model()->findByPk($idEquipo);
		if ($equipo === null) {
			throw new CHttpException(404, 'Equipo no encontrado.');
		}
		$rows = ProdeUsuario::getMiembrosEquipoConPuntos($idEquipo);
		$this->pageTitle = $equipo->Nombre . ' - Prode';
		$this->render('equipo_detalle', array('equipo' => $equipo, 'rows' => $rows));
	}

	public function actionCambiarEquipo()
	{
		ProdeSession::requireLogin();
		$user = ProdeSession::user();

		$idEq = isset($_POST['idEquipo']) ? (int)$_POST['idEquipo'] : 0;
		$nuevo = null;
		if ($idEq > 0) {
			$nuevo = Equipos::model()->findByPk($idEq);
			if ($nuevo === null) {
				Yii::app()->user->setFlash('prode_err', 'Equipo invalido.');
				$this->redirect(array('prode/panel'));
			}
		}
		$user->idEquipo = $nuevo ? (int)$nuevo->idEquipo : null;
		$user->save(false, array('idEquipo'));

		if ($nuevo) {
			Yii::app()->user->setFlash('prode_ok', 'Ahora jugas para ' . $nuevo->Nombre . '.');
		} else {
			Yii::app()->user->setFlash('prode_ok', 'Ya no estas en ningun equipo.');
		}
		$this->redirect(array('prode/panel'));
	}

	// ============================================================
	// CON LOGIN
	// ============================================================

	public function actionPanel()
	{
		ProdeSession::requireLogin();
		$user = ProdeSession::user();

		// Torneos I/A disponibles
		$criteria = new CDbCriteria;
		$criteria->condition = "Estado in ('I','A')";
		$criteria->order = 'Inicio DESC, idTorneo DESC';
		$torneos = Torneo::model()->findAll($criteria);

		// Fechas disponibles (de los torneos I/A, con partidos)
		$fechasPorTorneo = array();
		foreach ($torneos as $t) {
			$ps = Fixture::model()->ConsultaFixture((int)$t->idTorneo);
			$fechas = array();
			foreach ($ps as $p) {
				$n = (int)$p->NFecha;
				if (!in_array($n, $fechas, true)) $fechas[] = $n;
			}
			sort($fechas);
			$fechasPorTorneo[(int)$t->idTorneo] = $fechas;
		}

		// Predicciones anteriores del usuario
		$criteria = new CDbCriteria;
		$criteria->condition = 'idProdeUsuario = :id';
		$criteria->params = array(':id' => (int)$user->idProdeUsuario);
		$criteria->order = 'updatedAt DESC';
		$criteria->limit = 50;
		$misPreds = ProdePrediccion::model()->findAll($criteria);

		// Enriquecer con info del partido
		$historial = array();
		foreach ($misPreds as $pred) {
			$partido = Fixture::model()->findByPk((int)$pred->idFixture);
			if ($partido) {
				$historial[] = array('pred' => $pred, 'partido' => $partido);
			}
		}

		$this->pageTitle = 'Mi panel - Prode';
		$this->render('panel', array(
			'user' => $user,
			'torneos' => $torneos,
			'fechasPorTorneo' => $fechasPorTorneo,
			'historial' => $historial,
		));
	}

	public function actionPredict($idTorneo, $fecha)
	{
		ProdeSession::requireLogin();
		$user = ProdeSession::user();

		$idTorneo = (int)$idTorneo;
		$fecha = (int)$fecha;

		$torneo = Torneo::model()->findByPk($idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'Torneo no encontrado.');
		}
		if (!in_array($torneo->Estado, array('I', 'A'), true)) {
			throw new CHttpException(404, 'Torneo no disponible para pronosticar.');
		}

		$partidos = Fixture::model()->ConsultaFixture($idTorneo);
		$partidosFecha = array();
		foreach ($partidos as $p) {
			if ((int)$p->NFecha === $fecha) {
				$partidosFecha[] = $p;
			}
		}
		if (empty($partidosFecha)) {
			throw new CHttpException(404, 'No hay partidos cargados para esta fecha.');
		}

		// Lock: si la fecha ya empezo (Fecha del primer partido <= hoy), no se puede editar
		$primerPartido = $partidosFecha[0];
		$fechaPartido = $primerPartido->Fecha; // date
		$lock = strtotime($fechaPartido) <= time();

		// Cargar predicciones existentes del usuario
		$idsFixture = array();
		foreach ($partidosFecha as $p) $idsFixture[] = (int)$p->idFixture;
		$criteria = new CDbCriteria;
		$criteria->addInCondition('idFixture', $idsFixture);
		$criteria->compare('idProdeUsuario', (int)$user->idProdeUsuario);
		$existentes = ProdePrediccion::model()->findAll($criteria);
		$predPorFixture = array();
		foreach ($existentes as $p) {
			$predPorFixture[(int)$p->idFixture] = $p;
		}

		$mensaje = null;
		if (isset($_POST['predicciones']) && !$lock) {
			$okCount = 0;
			$errorCount = 0;
			foreach ($_POST['predicciones'] as $idFix => $data) {
				$idFix = (int)$idFix;
				if (!$idFix) continue;
				$gl = isset($data['golesLocal']) ? (int)$data['golesLocal'] : null;
				$gv = isset($data['golesVisitante']) ? (int)$data['golesVisitante'] : null;
				if ($gl === null || $gv === null) continue;
				if ($gl < 0 || $gl > 20 || $gv < 0 || $gv > 20) continue;

				$pred = isset($predPorFixture[$idFix]) ? $predPorFixture[$idFix] : new ProdePrediccion;
				$pred->idProdeUsuario = (int)$user->idProdeUsuario;
				$pred->idFixture = $idFix;
				$pred->golesLocal = $gl;
				$pred->golesVisitante = $gv;
				// puntos no se tocan aca
				if ($pred->save()) {
					$okCount++;
					$predPorFixture[$idFix] = $pred;
				} else {
					$errorCount++;
				}
			}
			if ($errorCount === 0) {
				$mensaje = 'Predicciones guardadas. Suerte!';
			} else {
				$mensaje = "Guardamos $okCount predicciones, pero $errorCount fallaron.";
			}
		}

		$this->pageTitle = 'Pronostico - ' . $torneo->Nombre . ' - Fecha ' . $fecha;
		$this->render('predict', array(
			'user' => $user,
			'torneo' => $torneo,
			'fecha' => $fecha,
			'partidos' => $partidosFecha,
			'predPorFixture' => $predPorFixture,
			'lock' => $lock,
			'mensaje' => $mensaje,
		));
	}

	// ============================================================
	// ADMIN (requiere esAdmin=1 en prode_usuario)
	// ============================================================

	public function actionAdmin()
	{
		ProdeSession::requireAdmin();
		$user = ProdeSession::user();

		$criteria = new CDbCriteria;
		$criteria->condition = "Estado in ('I','A')";
		$criteria->order = 'Inicio DESC, idTorneo DESC';
		$torneos = Torneo::model()->findAll($criteria);

		$fechasPorTorneo = array();
		$publicadasPorTorneo = array();
		foreach ($torneos as $t) {
			$ps = Fixture::model()->ConsultaFixture((int)$t->idTorneo);
			$fechas = array();
			foreach ($ps as $p) {
				$n = (int)$p->NFecha;
				if (!in_array($n, $fechas, true)) $fechas[] = $n;
			}
			sort($fechas);
			$fechasPorTorneo[(int)$t->idTorneo] = $fechas;

			$pub = ProdeFechaPublicada::model()->findAll('idTorneo = :t', array(':t' => (int)$t->idTorneo));
			$pubs = array();
			foreach ($pub as $pp) $pubs[(int)$pp->NFecha] = $pp;
			$publicadasPorTorneo[(int)$t->idTorneo] = $pubs;
		}

		$this->pageTitle = 'Admin - Prode';
		$this->render('admin_index', array(
			'user' => $user,
			'torneos' => $torneos,
			'fechasPorTorneo' => $fechasPorTorneo,
			'publicadasPorTorneo' => $publicadasPorTorneo,
		));
	}

	public function actionLoadResultados($idTorneo, $fecha)
	{
		ProdeSession::requireAdmin();
		$user = ProdeSession::user();
		$idTorneo = (int)$idTorneo;
		$fecha = (int)$fecha;

		$torneo = Torneo::model()->findByPk($idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'Torneo no encontrado.');
		}

		$partidos = Fixture::model()->ConsultaFixture($idTorneo);
		$partidosFecha = array();
		foreach ($partidos as $p) {
			if ((int)$p->NFecha === $fecha) {
				$partidosFecha[] = $p;
			}
		}

		$mensaje = null;
		if (isset($_POST['resultados'])) {
			foreach ($_POST['resultados'] as $idFix => $data) {
				$idFix = (int)$idFix;
				$partido = Fixture::model()->findByPk($idFix);
				if ($partido === null) continue;
				$esLibre = ((int)$partido->Visitante === 0);
				if ($esLibre) continue;
				$gl = isset($data['golesLocal']) && $data['golesLocal'] !== '' ? (int)$data['golesLocal'] : null;
				$gv = isset($data['golesVisitante']) && $data['golesVisitante'] !== '' ? (int)$data['golesVisitante'] : null;
				$partido->GolLocal = $gl;
				$partido->GolVisitante = $gv;
				$partido->save(false, array('GolLocal', 'GolVisitante'));
			}
			$mensaje = 'Resultados guardados. Podes publicar la fecha cuando termines.';
		}

		$this->pageTitle = 'Cargar resultados - Fecha ' . $fecha;
		$this->render('admin_load', array(
			'user' => $user,
			'torneo' => $torneo,
			'fecha' => $fecha,
			'partidos' => $partidosFecha,
			'mensaje' => $mensaje,
		));
	}

	public function actionPublicar($idTorneo, $fecha)
	{
		ProdeSession::requireAdmin();
		$user = ProdeSession::user();
		$idTorneo = (int)$idTorneo;
		$fecha = (int)$fecha;

		$torneo = Torneo::model()->findByPk($idTorneo);
		if ($torneo === null) {
			throw new CHttpException(404, 'Torneo no encontrado.');
		}

		// Recalcular puntos de esta fecha
		$partidos = Fixture::model()->ConsultaFixture($idTorneo);
		foreach ($partidos as $p) {
			if ((int)$p->NFecha !== $fecha) continue;
			if ((int)$p->Visitante === 0) continue; // libre
			if ($p->GolLocal === null || $p->GolVisitante === null) continue;
			$criteria = new CDbCriteria;
			$criteria->compare('idFixture', (int)$p->idFixture);
			$preds = ProdePrediccion::model()->findAll($criteria);
			foreach ($preds as $pred) {
				$pred->puntos = $pred->calcularPuntos((int)$p->GolLocal, (int)$p->GolVisitante);
				$pred->save(false, array('puntos'));
			}
		}

		ProdeFechaPublicada::marcarPublicada($idTorneo, $fecha, (int)$user->idProdeUsuario);

		Yii::app()->user->setFlash('prode_ok', 'Fecha ' . $fecha . ' publicada. Ya se ve en el ranking.');
		$this->redirect(array('prode/admin'));
	}

	public function actionRecalcular($idTorneo, $fecha)
	{
		ProdeSession::requireAdmin();
		$idTorneo = (int)$idTorneo;
		$fecha = (int)$fecha;

		$partidos = Fixture::model()->ConsultaFixture($idTorneo);
		$count = 0;
		foreach ($partidos as $p) {
			if ((int)$p->NFecha !== $fecha) continue;
			if ((int)$p->Visitante === 0) continue;
			if ($p->GolLocal === null || $p->GolVisitante === null) continue;
			$criteria = new CDbCriteria;
			$criteria->compare('idFixture', (int)$p->idFixture);
			$preds = ProdePrediccion::model()->findAll($criteria);
			foreach ($preds as $pred) {
				$pred->puntos = $pred->calcularPuntos((int)$p->GolLocal, (int)$p->GolVisitante);
				$pred->save(false, array('puntos'));
				$count++;
			}
		}

		Yii::app()->user->setFlash('prode_ok', "Recalculadas $count predicciones para la fecha $fecha.");
		$this->redirect(array('prode/admin'));
	}

	public function actionUsuarios()
	{
		ProdeSession::requireAdmin();
		$user = ProdeSession::user();

		if (isset($_POST['op'])) {
			$op = $_POST['op'];
			$id = isset($_POST['idProdeUsuario']) ? (int)$_POST['idProdeUsuario'] : 0;
			$target = ProdeUsuario::model()->findByPk($id);
			if ($target !== null && $target->idProdeUsuario !== (int)$user->idProdeUsuario) {
				if ($op === 'toggleActivo') {
					$target->activo = $target->activo ? 0 : 1;
					$target->save(false, array('activo'));
				} elseif ($op === 'toggleAdmin') {
					$target->esAdmin = $target->esAdmin ? 0 : 1;
					$target->save(false, array('esAdmin'));
				}
			}
		}

		$criteria = new CDbCriteria;
		$criteria->order = 'createdAt DESC';
		$usuarios = ProdeUsuario::model()->findAll($criteria);

		$this->pageTitle = 'Usuarios - Prode Admin';
		$this->render('admin_usuarios', array(
			'user' => $user,
			'usuarios' => $usuarios,
		));
	}
}
