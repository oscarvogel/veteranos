<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<head>
	<meta charset="utf-8" />
	<meta name="language" content="<?php echo Yii::app()->language;?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-1631748295721219",
        enable_page_level_ads: true
      });
    </script>
	<?php 
        $cs = Yii::app()->clientScript;
        $detect = Yii::app()->mobileDetect;

        $cs->scriptMap['jquery.js'] = 'https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js';
        $cs->scriptMap['jquery.min.js'] = 'https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js';
        $cs->registerCoreScript('jquery');

        $cs->registerScriptFile("https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.js");

        $cs->registerCssFile("https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css");
        $cs->registerCssFile("https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.css");
        $cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/main.css?v=20260627-admin-balanced');
        $cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/form.css');
        $cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/sistema.css');

        $cs->registerScriptFile("https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js", CClientScript::POS_HEAD);

    ?>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="wrapper">

	<nav class="navbar navbar-inverse navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-main">
					<span class="sr-only">Menu</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><?php echo CHtml::encode(Yii::app()->name); ?></a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-main">
				<ul class="nav navbar-nav">
					<li><a href="/">INICIO</a></li>
					<?php
					// Prode: dropdown con accesos segun login + admin del prode.
					// ProdeSession::user() puede ser null (si el componente no esta
					// disponible, no rompe gracias al try/catch).
					$prodeUser = null;
					try {
						if (class_exists('ProdeSession', false)) {
							$prodeUser = ProdeSession::user();
						}
					} catch (Exception $e) { $prodeUser = null; }
					$esAdminProde = ($prodeUser !== null && (int)$prodeUser->esAdmin === 1);
					$logueadoProde = ($prodeUser !== null);
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">🎯 Prode <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Yii::app()->createUrl('/prode/index')?>">Inicio Prode</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/prode/ranking')?>">Ranking individual</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/prode/rankingEquipos')?>">Ranking por equipos</a></li>
							<?php if ($logueadoProde): ?>
								<li role="separator" class="divider"></li>
								<li><a href="<?php echo Yii::app()->createUrl('/prode/panel')?>">Mi panel</a></li>
								<?php if ($esAdminProde): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/prode/admin')?>">🔒 Admin Prode</a></li>
									<li><a href="<?php echo Yii::app()->createUrl('/prode/usuarios')?>">👥 Gestionar usuarios del prode</a></li>
								<?php endif; ?>
								<li><a href="<?php echo Yii::app()->createUrl('/prode/logout')?>">Cerrar sesi&oacute;n</a></li>
							<?php else: ?>
								<li role="separator" class="divider"></li>
								<li><a href="<?php echo Yii::app()->createUrl('/prode/login')?>">Iniciar sesi&oacute;n</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/prode/register')?>">Crear cuenta</a></li>
							<?php endif; ?>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Consultas <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Yii::app()->createUrl('/posiciones/index')?>">Posiciones</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/posiciones/resultados')?>">Resultados</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/posiciones/resultados', array('modo'=>'resumen'))?>">Resumen de Fecha</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/tarjetas/consulta')?>">Tarjetas</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/tarjetas/tarjetasequipo')?>">Tarjetas por Equipo</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/tarjetas/fairplay')?>">Juego Limpio</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/goles/goleador')?>">Goleadores</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/equipos/ListaBuenaFe')?>">Listas de Buena Fe</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/site/fixture')?>">Fixture</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/fixture/ConsultaAsignaciones')?>">Canchas y arbitros</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/resoluciones/index')?>">Resoluciones</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/jugador/historia')?>">Historia Jugador</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/planillas/index')?>">Planillas</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Informacion <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_transgreciones_y_penas.pdf">Reglamento Transgreciones y Penas</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/Reglamento_Transgresiones_y_Penas_AFA.pdf">Reglamento Transgreciones y Penas AFA</a></li>
							<li><a href="<?php echo Yii::app()->createUrl('/site/page', array('view'=>'reglamento'))?>">Reglamento 2014</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_2017.pdf">Reglamento 2017</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_2018.pdf">Reglamento 2018</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_2019.pdf">Reglamento 2019</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_2021.pdf">Reglamento 2021</a></li>
							<li><a href="http://www.veteranos.ar/media/pdf/reglamento_2025.pdf">Reglamento 2025</a></li>
						</ul>
					</li>
					<?php if(!Yii::app()->user->isGuest){?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Tablas <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo Yii::app()->createUrl('/torneo/admin')?>">Torneo</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/equipos/admin')?>">Equipos</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/equipostorneo/admin')?>">Equipos por Torneo</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/jugador/admin')?>">Jugadores</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/jugador/documentacion')?>">Documentacion</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/articulos/admin')?>">Articulos</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/arbitros/admin')?>">Arbitros</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/canchas/admin')?>">Canchas</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/fixture/CargaResultadosFecha')?>">Fixture</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/fixture/ModificaAsignaciones')?>">Modifica Asignaciones</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/fixture/CorrerFechas')?>">Reprogramar fecha</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/posicionestorneo/admin')?>">Posiciones Torneo</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/ingresos/admin')?>">Ingresos</a></li>
							</ul>
						</li>
						<?php $puedeCuotasSociales = Yii::app()->user->checkAccess('action_sociosCuota_equipo') || Yii::app()->user->checkAccess('action_ingresos_create') || Yii::app()->user->checkAccess('action_ingresos_admin'); ?>
						<?php if(Yii::app()->user->checkAccess('action_ingresos_arqueoCaja') || Yii::app()->user->checkAccess('action_ingresos_create') || Yii::app()->user->checkAccess('action_ingresos_resumenMensual') || Yii::app()->user->checkAccess('action_conceptos_admin') || $puedeCuotasSociales || Yii::app()->user->checkAccess('action_sociosCuota_informe')): ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Caja <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?php if($puedeCuotasSociales): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/sociosCuota/equipo')?>">Cuotas sociales</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_sociosCuota_informe') || $puedeCuotasSociales): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/sociosCuota/informe')?>">Informe cuotas sociales</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_ingresos_create')): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/ingresos/create')?>">Registrar pago</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_ingresos_arqueoCaja')): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/ingresos/arqueoCaja')?>">Arqueo de caja</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_ingresos_resumenMensual')): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/ingresos/resumenMensual')?>">Resumen mensual</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_ingresos_admin')): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/ingresos/admin')?>">Recibos</a></li>
								<?php endif; ?>
								<?php if(Yii::app()->user->checkAccess('action_conceptos_admin')): ?>
									<li><a href="<?php echo Yii::app()->createUrl('/conceptos/admin')?>">Conceptos de cobro</a></li>
								<?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Equipos <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo Yii::app()->createUrl('/jugador/asignaequipo')?>">Asignacion de jugadores</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/jugador/listajugadores')?>">Lista de jugadores</a></li>
								<li><a href="<?php echo Yii::app()->createUrl('/jugador/Jugadorequipo')?>">Jugadores de equipos</a></li>
							</ul>
						</li>
					<?php } ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo (Yii::app()->user->isGuest) ? '#' : Yii::app()->user->ui->userManagementAdminUrl;?>">Administrar Usuarios</a></li>
						</ul>
					</li>
					<li><a href="<?php echo (Yii::app()->user->isGuest) ? Yii::app()->user->ui->loginUrl : Yii::app()->user->ui->logoutUrl;?>"><span class="glyphicon glyphicon-lock"></span></a></li>
				</ul>
			</div>
		</div>
	</nav>

	<?php $contentClass = (strpos($this->layout, 'column2') !== false && !Yii::app()->user->isGuest) ? ' admin-page-container' : ''; ?>
	<div class="container main-content<?php echo $contentClass; ?>">
		<?php if(isset($this->breadcrumbs)):?>
			<?php $this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
			)); ?>
		<?php endif?>

		<?php echo $content; ?>
	</div>

</div>

<footer class="site-footer">
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				Copyright &copy; <?php echo date('Y'); ?> Veteranos. Todos los derechos reservados.
			</div>
			<div class="col-sm-6 text-right">
				<?php echo Yii::powered(); ?>
			</div>
		</div>
	</div>
</footer>

</body>
</html>
