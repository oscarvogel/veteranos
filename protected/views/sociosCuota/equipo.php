<?php
$this->breadcrumbs=array('Cuotas sociales');
$this->menu=array(
	array('label'=>'Gestionar por equipo', 'url'=>array('equipo')),
	array('label'=>'Informe de cuotas', 'url'=>array('informe')),
);

Yii::app()->clientScript->registerCss('cuotasSocialesEquipo', '
	.cuotas-panel { background:#fff; border:1px solid #dbe3ee; border-radius:8px; padding:18px; margin-bottom:18px; }
	.cuotas-filtros { display:grid; grid-template-columns: minmax(240px, 1fr) 160px auto; gap:12px; align-items:end; }
	.cuotas-filtros label { display:block; font-weight:bold; margin-bottom:5px; }
	.cuotas-filtros select, .cuotas-filtros input { width:100%; height:34px; box-sizing:border-box; }
	.cuotas-actions { margin:12px 0; display:flex; gap:8px; flex-wrap:wrap; }
	.cuotas-badge { display:inline-block; padding:3px 7px; border-radius:4px; font-size:12px; font-weight:bold; }
	.cuotas-badge-socio { background:#dff0d8; color:#2b542c; }
	.cuotas-badge-no-socio { background:#f5f5f5; color:#666; }
	.cuotas-badge-pagado { background:#d9edf7; color:#245269; }
	.cuotas-badge-pendiente { background:#fcf8e3; color:#8a6d3b; }
	.cuotas-public-link { word-break:break-all; }
	@media (max-width: 760px) { .cuotas-filtros { grid-template-columns:1fr; } }
');
?>

<h1>Cuotas sociales</h1>

<?php foreach(array('success'=>'success', 'error'=>'danger') as $flash=>$type): ?>
	<?php if(Yii::app()->user->hasFlash($flash)): ?>
		<div class="alert alert-<?php echo $type; ?>"><?php echo CHtml::encode(Yii::app()->user->getFlash($flash)); ?></div>
	<?php endif; ?>
<?php endforeach; ?>

<div class="cuotas-panel">
	<?php echo CHtml::beginForm(array('equipo'), 'get', array('class'=>'cuotas-filtros')); ?>
		<div>
			<?php echo CHtml::label('Equipo', 'idEquipo'); ?>
			<?php echo CHtml::dropDownList('idEquipo', $idEquipo, Equipos::getListEquipo(), array('empty'=>'Seleccione equipo', 'id'=>'idEquipo')); ?>
		</div>
		<div>
			<?php echo CHtml::label('Periodo', 'periodo'); ?>
			<?php echo CHtml::textField('periodo', $periodo, array('id'=>'periodo', 'maxlength'=>7, 'placeholder'=>'aaaa-mm')); ?>
		</div>
		<div>
			<?php echo CHtml::submitButton('Consultar', array('class'=>'btn btn-primary')); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>

<?php if($estado !== null): ?>
	<div class="cuotas-panel">
		<h3><?php echo CHtml::encode($estado['equipo']['Nombre']); ?> - <?php echo CHtml::encode($estado['periodo']); ?></h3>
		<p>
			Socios: <strong><?php echo (int)$estado['totales']['socios']; ?></strong> |
			Al dia: <strong><?php echo (int)$estado['totales']['alDia']; ?></strong> |
			Pendientes: <strong><?php echo (int)$estado['totales']['pendientes']; ?></strong> |
			No socios: <strong><?php echo (int)$estado['totales']['noSocios']; ?></strong>
		</p>
		<p class="cuotas-public-link">
			Link publico:
			<?php
			$publicUrl = Yii::app()->createAbsoluteUrl('/sociosCuota/estadoEquipo', array(
				'idEquipo'=>$estado['equipo']['idEquipo'],
				'periodo'=>$estado['periodo'],
			));
			echo CHtml::link(CHtml::encode($publicUrl), $publicUrl, array('target'=>'_blank'));
			?>
		</p>
	</div>

	<?php echo CHtml::beginForm(array('guardar'), 'post'); ?>
		<?php echo CHtml::hiddenField('idEquipo', $estado['equipo']['idEquipo']); ?>
		<?php echo CHtml::hiddenField('periodo', $estado['periodo']); ?>
		<div class="cuotas-actions">
			<?php echo CHtml::button('Marcar socios del equipo como pagados', array('class'=>'btn btn-default', 'id'=>'marcar-socios-pagados')); ?>
			<?php echo CHtml::submitButton('Guardar cuotas', array('class'=>'btn btn-primary')); ?>
			<?php echo CHtml::link('Ver informe', array('informe', 'idEquipo'=>$estado['equipo']['idEquipo'], 'periodo'=>$estado['periodo']), array('class'=>'btn btn-info')); ?>
		</div>

		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Jugador</th>
					<th>Socio</th>
					<th>Estado</th>
					<th>Marcar socio</th>
					<th>Pagado en periodo</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($estado['jugadores'] as $jugador): ?>
					<tr>
						<td><?php echo CHtml::encode($jugador['Nombre']); ?></td>
						<td>
							<span class="cuotas-badge <?php echo $jugador['esSocio'] ? 'cuotas-badge-socio' : 'cuotas-badge-no-socio'; ?>">
								<?php echo $jugador['esSocio'] ? 'Socio' : 'No socio'; ?>
							</span>
						</td>
						<td>
							<span class="cuotas-badge <?php echo $jugador['pagado'] ? 'cuotas-badge-pagado' : 'cuotas-badge-pendiente'; ?>">
								<?php echo CHtml::encode($jugador['estado']); ?>
							</span>
						</td>
						<td><?php echo CHtml::checkBox('socios[]', $jugador['esSocio'], array('value'=>$jugador['idJugador'], 'class'=>'cuota-socio-check')); ?></td>
						<td><?php echo CHtml::checkBox('pagados[]', $jugador['pagado'], array('value'=>$jugador['idJugador'], 'class'=>'cuota-pagado-check')); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php echo CHtml::endForm(); ?>

	<?php Yii::app()->clientScript->registerScript('cuotasSocialesEquipo', "
		$('#marcar-socios-pagados').on('click', function(){
			$('.cuota-pagado-check').prop('checked', false);
			$('.cuota-socio-check:checked').each(function(){
				$(this).closest('tr').find('.cuota-pagado-check').prop('checked', true);
			});
		});
	"); ?>
<?php endif; ?>
