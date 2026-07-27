<?php
$this->breadcrumbs=array('Cuotas sociales'=>array('equipo'), 'Informe');
$this->menu=array(
	array('label'=>'Gestionar por equipo', 'url'=>array('equipo')),
	array('label'=>'Informe de cuotas', 'url'=>array('informe')),
);

Yii::app()->clientScript->registerCss('cuotasSocialesInforme', '
	.cuotas-panel { background:#fff; border:1px solid #dbe3ee; border-radius:8px; padding:18px; margin-bottom:18px; }
	.cuotas-filtros { display:grid; grid-template-columns: minmax(220px, 1fr) 150px 160px auto; gap:12px; align-items:end; }
	.cuotas-filtros label { display:block; font-weight:bold; margin-bottom:5px; }
	.cuotas-filtros select, .cuotas-filtros input { width:100%; height:34px; box-sizing:border-box; }
	.cuotas-section-title { margin-top:24px; }
	.cuotas-public-link { word-break:break-all; }
	@media (max-width: 760px) { .cuotas-filtros { grid-template-columns:1fr; } }
');
?>

<h1>Informe de cuotas sociales</h1>

<div class="cuotas-panel">
	<?php echo CHtml::beginForm(array('informe'), 'get', array('class'=>'cuotas-filtros')); ?>
		<div>
			<?php echo CHtml::label('Equipo', 'idEquipo'); ?>
			<?php echo CHtml::dropDownList('idEquipo', $idEquipo, Equipos::getListEquipo(), array('empty'=>'Seleccione equipo', 'id'=>'idEquipo')); ?>
		</div>
		<div>
			<?php echo CHtml::label('Periodo', 'periodo'); ?>
			<?php echo CHtml::textField('periodo', $periodo, array('id'=>'periodo', 'maxlength'=>7, 'placeholder'=>'aaaa-mm')); ?>
		</div>
		<div>
			<?php echo CHtml::label('Estado', 'estado'); ?>
			<?php echo CHtml::dropDownList('estado', $estadoFiltro, array(
				''=>'Todos',
				'alDia'=>'Al dia',
				'pendiente'=>'Pendiente',
				'noSocio'=>'No socio',
			), array('id'=>'estado')); ?>
		</div>
		<div>
			<?php echo CHtml::submitButton('Filtrar', array('class'=>'btn btn-primary')); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>

<?php if(!$hayFiltro): ?>
	<div class="alert alert-info">
		Seleccione un equipo o un estado para consultar el informe. No se muestran todos los jugadores por defecto para que la pantalla sea rapida y clara.
	</div>
<?php endif; ?>

<?php if($idEquipo !== ''): ?>
	<div class="cuotas-panel cuotas-public-link">
		<?php $publicUrl = Yii::app()->createAbsoluteUrl('/sociosCuota/estadoEquipo', array('idEquipo'=>$idEquipo, 'periodo'=>$periodo)); ?>
		Link publico del equipo: <?php echo CHtml::link(CHtml::encode($publicUrl), $publicUrl, array('target'=>'_blank')); ?>
	</div>
<?php endif; ?>

<?php if($hayFiltro): ?>
	<div class="cuotas-panel">
		<strong>Resumen:</strong>
		Al dia: <?php echo count($informe['alDia']); ?> |
		Pendientes: <?php echo count($informe['pendientes']); ?> |
		No socios: <?php echo count($informe['noSocios']); ?>
	</div>

	<?php $this->widget('bootstrap.widgets.TbGridView', array(
		'id'=>'cuotas-informe-grid',
		'dataProvider'=>$dataProvider,
		'type'=>'striped bordered',
		'enableSorting'=>true,
		'summaryText'=>'Mostrando {start}-{end} de {count} registros.',
		'emptyText'=>'Sin registros para el filtro seleccionado.',
		'columns'=>array(
			array(
				'name'=>'Nombre',
				'header'=>'Jugador',
				'value'=>'$data["Nombre"]',
			),
			array(
				'name'=>'Equipo',
				'header'=>'Equipo',
				'value'=>'$data["Equipo"]',
			),
			array(
				'name'=>'estado',
				'header'=>'Estado',
				'value'=>'$data["estado"]',
			),
		),
	)); ?>
<?php endif; ?>
