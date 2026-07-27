<?php
Yii::app()->clientScript->registerCss('cuotasSocialesPublico', '
	.estado-cuotas { max-width:960px; margin:24px auto; }
	.estado-cuotas h1 { margin-bottom:6px; }
	.estado-cuotas-resumen { color:#555; margin-bottom:18px; }
	.estado-badge { display:inline-block; padding:3px 7px; border-radius:4px; font-size:12px; font-weight:bold; }
	.estado-socio { background:#dff0d8; color:#2b542c; }
	.estado-no-socio { background:#f5f5f5; color:#666; }
	.estado-pagado { background:#d9edf7; color:#245269; }
	.estado-pendiente { background:#fcf8e3; color:#8a6d3b; }
');
?>

<div class="estado-cuotas">
	<h1>Estado de cuotas sociales</h1>
	<p class="estado-cuotas-resumen">
		<?php echo CHtml::encode($estado['equipo']['Nombre']); ?> -
		Periodo <?php echo CHtml::encode($estado['periodo']); ?>
	</p>
	<p>
		Socios: <strong><?php echo (int)$estado['totales']['socios']; ?></strong> |
		Al dia: <strong><?php echo (int)$estado['totales']['alDia']; ?></strong> |
		Pendientes: <strong><?php echo (int)$estado['totales']['pendientes']; ?></strong> |
		No socios: <strong><?php echo (int)$estado['totales']['noSocios']; ?></strong>
	</p>

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Jugador</th>
				<th>Socio</th>
				<th>Estado</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($estado['jugadores'] as $jugador): ?>
				<tr>
					<td><?php echo CHtml::encode($jugador['Nombre']); ?></td>
					<td>
						<span class="estado-badge <?php echo $jugador['esSocio'] ? 'estado-socio' : 'estado-no-socio'; ?>">
							<?php echo $jugador['esSocio'] ? 'Socio' : 'No socio'; ?>
						</span>
					</td>
					<td>
						<span class="estado-badge <?php echo $jugador['pagado'] ? 'estado-pagado' : 'estado-pendiente'; ?>">
							<?php echo CHtml::encode($jugador['estado']); ?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
