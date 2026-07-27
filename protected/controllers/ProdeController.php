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

		// Lock: el usuario puede editar hasta el dia ANTERIOR al partido.
		// Si se juega el 1/8, puede editar hasta el 31/7 inclusive.
		// El 1/8 ya esta bloqueado desde las 00:00.
		$primerPartido = $partidosFecha[0];
		$lock = self::partidoEstaBloqueado($primerPartido, 0, false);

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

		// Calcular probabilidades estimadas para cada partido (60% historico,
		// 40% rendimiento actual). Solo aplica a partidos con local y visitante
		// (no libres).
		$probabilidades = array();
		foreach ($partidosFecha as $p) {
			if ((int)$p->Visitante === 0) {
				$probabilidades[(int)$p->idFixture] = null;
				continue;
			}
			$probabilidades[(int)$p->idFixture] = self::calcularProbabilidades(
				(string)$p->Local,
				(string)$p->Visitante,
				(int)$p->idTorneo
			);
		}

		$this->pageTitle = 'Pronostico - ' . $torneo->Nombre . ' - Fecha ' . $fecha;
		$this->render('predict', array(
			'user' => $user,
			'torneo' => $torneo,
			'fecha' => $fecha,
			'partidos' => $partidosFecha,
			'predPorFixture' => $predPorFixture,
			'probabilidades' => $probabilidades,
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

		// Lock del admin: puede editar/cargar resultados hasta el dia
		// ANTERIOR al partido. Si se juega el 1/8, puede cargar hasta el
		// 31/7 inclusive. El 1/8 ya esta bloqueado desde las 00:00.
		$primerPartido = !empty($partidosFecha) ? $partidosFecha[0] : null;
		$lockResultados = $primerPartido === null ? false : self::partidoEstaBloqueado($primerPartido, 0, false);

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
			'lock' => $lockResultados,
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

		// Lock: el admin no puede publicar el dia del partido. Si se juega
		// el 1/8, el 1/8 ya esta bloqueado desde las 00:00 y no se puede
		// publicar hasta despues (lo cual no tiene sentido, el partido ya
		// se jugo). En la practica el admin deberia publicar al cargar
		// los resultados, antes de la medianoche del dia del partido.
		$partidosPrecheck = Fixture::model()->ConsultaFixture($idTorneo);
		$primerPartido = null;
		foreach ($partidosPrecheck as $p) {
			if ((int)$p->NFecha === $fecha) { $primerPartido = $p; break; }
		}
		if ($primerPartido !== null && self::partidoEstaBloqueado($primerPartido, 0, false)) {
			Yii::app()->user->setFlash('prode_err', 'No se puede publicar la fecha: ya paso la fecha limite (inicio del dia del partido).');
			$this->redirect(array('prode/admin'));
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

	// ============================================================
	// HELPERS
	// ============================================================

	/**
	 * Devuelve true si la edicion del partido esta bloqueada.
	 *
	 * Comportamiento: el lock se activa al INICIO DEL DIA del partido
	 * (medianoche), sin importar la hora. Si el partido es el 1/8 a las
	 * 15:00, ya no se puede editar desde el 1/8 a las 00:00. El dia
	 * anterior (31/7) se puede editar todo el dia.
	 *
	 * @param object $partido Row de fixture con Fecha (Y-m-d) y Hora (H:i:s).
	 * @param int $margenSegundos Segundos previos que se consideran "bloqueados".
	 *        0 = bloquea al inicio del dia del partido (default).
	 *        > 0 = bloquea N segundos antes.
	 * @param bool $usarHora Si es true, usa la columna Hora (lock por hora del partido).
	 *        Si es false (default), ignora la hora y bloquea al inicio del dia.
	 * @return bool
	 */
	public static function partidoEstaBloqueado($partido, $margenSegundos = 0, $usarHora = false)
	{
		if ($partido === null) return false;
		$fecha = (string)$partido->Fecha; // Y-m-d
		$hora = isset($partido->Hora) ? (string)$partido->Hora : '';
		if ($usarHora && $hora !== '' && $hora !== '00:00:00' && $hora !== '00:00') {
			$ts = strtotime($fecha . ' ' . $hora);
		} else {
			$ts = strtotime($fecha . ' 00:00:00');
		}
		if ($ts === false) return false;
		return (time() >= ($ts - (int)$margenSegundos));
	}

	/**
	 * Calcula las probabilidades estimadas de un partido: victoria local,
	 * empate, victoria visitante. Mezcla 60% historico entre los dos equipos
	 * (en cualquier torneo) con 40% rendimiento actual (ultimos N partidos
	 * de cada uno en el torneo indicado).
	 *
	 * Si no hay datos suficientes (sin historico y sin partidos en el
	 * torneo actual), devuelve null.
	 *
	 * @param string $idLocal  idEquipo del local.
	 * @param string $idVisit idEquipo del visitante.
	 * @param int $idTorneo   idTorneo del torneo en curso.
	 * @return array|null  Array con 'local', 'empate', 'visitante' (en %)
	 *                     y 'muestras' (info para mostrar al usuario).
	 */
	public static function calcularProbabilidades($idLocal, $idVisit, $idTorneo)
	{
		$idLocal = (string)$idLocal;
		$idVisit = (string)$idVisit;
		$idTorneo = (int)$idTorneo;

		// --- Historico entre los dos equipos (en cualquier torneo) -------
		$sqlHist = "SELECT Local, Visitante, GolLocal, GolVisitante FROM fixture
					WHERE GolLocal IS NOT NULL AND GolVisitante IS NOT NULL
					  AND ((Local = :a AND Visitante = :b)
						OR (Local = :b AND Visitante = :a))";
		$rows = Yii::app()->db->createCommand($sqlHist)
			->bindValues(array(':a' => $idLocal, ':b' => $idVisit))
			->queryAll();

		$histLocal = 0; $histEmpate = 0; $histVisit = 0;
		foreach ($rows as $r) {
			$gl = (int)$r['GolLocal'];
			$gv = (int)$r['GolVisitante'];
			if ($gl === $gv) { $histEmpate++; continue; }
			// Determinamos quien gano y mapeamos al equipo de interes
			$ganador = $gl > $gv ? $r['Local'] : $r['Visitante'];
			if ((string)$ganador === $idLocal) $histLocal++;
			else $histVisit++;
		}
		$totalHist = $histLocal + $histEmpate + $histVisit;

		// --- Rendimiento actual (ultimos N partidos de cada uno en el torneo)
		$N = 5;
		$sqlAct = "SELECT Local, Visitante, GolLocal, GolVisitante, Fecha
				   FROM fixture
				   WHERE idTorneo = :t
					 AND GolLocal IS NOT NULL AND GolVisitante IS NOT NULL
					 AND (Local = :eq OR Visitante = :eq)
				   ORDER BY Fecha DESC, idFixture DESC
				   LIMIT :lim";
		$rowsL = Yii::app()->db->createCommand($sqlAct)
			->bindValues(array(':t' => $idTorneo, ':eq' => $idLocal, ':lim' => $N))
			->queryAll();
		$rowsV = Yii::app()->db->createCommand($sqlAct)
			->bindValues(array(':t' => $idTorneo, ':eq' => $idVisit, ':lim' => $N))
			->queryAll();

		$calcFuerza = function($rows, $idEq) {
			$v = 0; $e = 0; $d = 0;
			foreach ($rows as $r) {
				$gl = (int)$r['GolLocal']; $gv = (int)$r['GolVisitante'];
				if ($gl === $gv) { $e++; continue; }
				$ganador = $gl > $gv ? $r['Local'] : $r['Visitante'];
				if ((string)$ganador === (string)$idEq) $v++;
				else $d++;
			}
			$total = count($rows);
			if ($total === 0) return array('fuerza' => 0.5, 'total' => 0);
			// fuerza = (V + 0.5*E) / total
			$fuerza = ($v + 0.5 * $e) / $total;
			return array('fuerza' => $fuerza, 'total' => $total, 'v' => $v, 'e' => $e, 'd' => $d);
		};
		$infoL = $calcFuerza($rowsL, $idLocal);
		$infoV = $calcFuerza($rowsV, $idVisit);

		// Si no hay datos suficientes, devolver null
		if ($totalHist === 0 && $infoL['total'] === 0 && $infoV['total'] === 0) {
			return null;
		}

		// --- Calculo de probabilidades actuales (con factor localia) ------
		$factorLocalia = 0.10;
		$fL = $infoL['fuerza']; $fV = $infoV['fuerza'];
		$actLocalRaw = $fL + $factorLocalia;
		$actVisitRaw = $fV;
		// Empate: mayor cuando los dos equipos tienen fuerza similar
		$diff = abs($fL - $fV);
		$actEmpateRaw = (1 - $diff) * 0.40;
		$sumaAct = $actLocalRaw + $actEmpateRaw + $actVisitRaw;
		if ($sumaAct <= 0) $sumaAct = 1;
		$actLocal = $actLocalRaw / $sumaAct;
		$actEmpate = $actEmpateRaw / $sumaAct;
		$actVisit = $actVisitRaw / $sumaAct;

		// --- Historico normalizado ---------------------------------------
		if ($totalHist > 0) {
			$histPctLocal = $histLocal / $totalHist;
			$histPctEmpate = $histEmpate / $totalHist;
			$histPctVisit = $histVisit / $totalHist;
			$pesoHist = 0.6;
		} else {
			$histPctLocal = $actLocal;
			$histPctEmpate = $actEmpate;
			$histPctVisit = $actVisit;
			$pesoHist = 0;
		}

		// --- Combinacion 60/40 (o 100% actual si no hay historico) -------
		$pesoAct = 1 - $pesoHist;
		$pLocal = $pesoHist * $histPctLocal + $pesoAct * $actLocal;
		$pEmpate = $pesoHist * $histPctEmpate + $pesoAct * $actEmpate;
		$pVisit = $pesoHist * $histPctVisit + $pesoAct * $actVisit;
		// Renormalizar por seguridad
		$suma = $pLocal + $pEmpate + $pVisit;
		if ($suma > 0) {
			$pLocal /= $suma; $pEmpate /= $suma; $pVisit /= $suma;
		}

		return array(
			'local'    => round($pLocal * 100),
			'empate'   => round($pEmpate * 100),
			'visitante' => round($pVisit * 100),
			'muestras' => array(
				'historico'   => $totalHist,
				'local_act'   => $infoL['total'],
				'visit_act'   => $infoV['total'],
				'hist_local'  => $histLocal,
				'hist_empate' => $histEmpate,
				'hist_visit'  => $histVisit,
			),
		);
	}
}
