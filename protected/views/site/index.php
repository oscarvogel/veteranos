<style>
	.home-positions{margin:0 0 28px 0;color:#071b39}
	.home-positions .home-header{background:#071b39;color:#fff;padding:22px 26px;margin-bottom:18px;border-radius:3px}
	.home-positions .home-title{font-size:28px;font-weight:700;line-height:34px;margin:0;color:#fff!important}
	.home-positions .home-subtitle{font-size:14px;color:#d8e3f3;margin-top:6px}
	.home-positions .torneo-card{background:#fff;border:1px solid #d8e0ea;border-radius:3px;margin-bottom:22px;box-shadow:0 1px 2px rgba(7,27,57,.06)}
	.home-positions .torneo-head{background:#f3f6fa;border-bottom:1px solid #d8e0ea;padding:12px 15px}
	.home-positions .torneo-name{font-size:18px;font-weight:700;margin:0;color:#071b39}
	.home-positions .torneo-meta{font-size:12px;color:#56677f;margin-top:2px}
	.home-positions .table-wrap{overflow-x:auto}
	.home-positions table{margin:0}
	.home-positions th{background:#071b39;color:#fff;font-size:11px;text-transform:uppercase;letter-spacing:.4px;border-color:#071b39!important;white-space:nowrap}
	.home-positions td{vertical-align:middle!important;border-color:#e0e6ee!important}
	.home-positions .rank{width:54px;text-align:center;font-weight:700;color:#006b3f}
	.home-positions .team{font-weight:700}
	.home-positions .points{font-weight:700;background:#edf5ff;text-align:center}
	.home-positions .number{text-align:center}
	.home-positions .empty{padding:20px;color:#667085}
	.home-positions .top-zone td{background:#e8f4ed}
	.home-prode-cta{display:block;background:linear-gradient(135deg,#078a48 0%,#063f2a 100%);color:#fff;border-radius:8px;padding:20px 24px;margin-bottom:18px;text-decoration:none;box-shadow:0 2px 8px rgba(6,63,42,.18);transition:transform .15s}
	.home-prode-cta:hover{color:#fff;text-decoration:none;transform:translateY(-1px)}
	.home-prode-cta-kicker{display:inline-block;background:rgba(255,255,255,.18);border-radius:12px;padding:2px 10px;font-size:11px;font-weight:700;letter-spacing:.06em;margin-bottom:6px}
	.home-prode-cta-title{font-size:24px;font-weight:800;line-height:28px;margin:0 0 4px;color:#fff!important}
	.home-prode-cta-sub{font-size:14px;margin:0;opacity:.95}
	.home-positions .clasifica{display:inline-block;border:1px solid #7fb796;background:#d9efe3;color:#006b3f;font-size:10px;font-weight:700;text-transform:uppercase;padding:2px 5px;border-radius:2px}
	@media (max-width: 767px){
		.home-positions .home-header{padding:18px 16px}
		.home-positions .home-title{font-size:22px;line-height:27px}
		.home-positions th,.home-positions td{font-size:12px}
		.home-positions .optional-stat{display:none}
	}
</style>

<div class="home-positions">
	<div class="home-header">
		<h1 class="home-title">Tablas de Posiciones</h1>
		<div class="home-subtitle">Torneos activos - los primeros 8 equipos quedan marcados como zona de clasificacion.</div>
	</div>

	<a class="home-prode-cta" href="<?php echo Yii::app()->createUrl('/prode/index')?>">
		<div>
			<div class="home-prode-cta-kicker">🎯 NUEVO</div>
			<div class="home-prode-cta-title">Particip&aacute; del Prode</div>
			<div class="home-prode-cta-sub">Hacé tu pron&oacute;stico de cada fecha. Sin plata, solo por los puntos. <strong>Entr&aacute; ahora &rarr;</strong></div>
		</div>
	</a>

	<?php if(empty($posicionesTorneos)){ ?>
		<div class="torneo-card">
			<div class="empty">No hay torneos activos para mostrar.</div>
		</div>
	<?php } ?>

	<?php foreach($posicionesTorneos as $bloque){
		$torneo = $bloque['torneo'];
		$posiciones = $bloque['posiciones'];
	?>
		<div class="torneo-card">
			<div class="torneo-head">
				<h2 class="torneo-name"><?php echo CHtml::encode($torneo->Nombre); ?></h2>
				<div class="torneo-meta">Tabla actualizada con los resultados cargados del torneo.</div>
			</div>
			<div class="table-wrap">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th class="rank">#</th>
							<th>Equipo</th>
							<th class="number">Estado</th>
							<th class="number">PJ</th>
							<th class="number optional-stat">PG</th>
							<th class="number optional-stat">PE</th>
							<th class="number optional-stat">PP</th>
							<th class="number optional-stat">GF</th>
							<th class="number optional-stat">GC</th>
							<th class="number">Dif.</th>
							<th class="number">Pts</th>
						</tr>
					</thead>
					<tbody>
						<?php if(empty($posiciones)){ ?>
							<tr><td colspan="11" class="empty">Sin equipos cargados para este torneo.</td></tr>
						<?php } ?>
						<?php $i = 1; foreach($posiciones as $dato){
							$clasifica = $i <= 8;
						?>
							<tr<?php echo $clasifica ? ' class="top-zone"' : ''; ?>>
								<td class="rank"><?php echo $i; ?></td>
								<td class="team"><?php echo CHtml::encode($dato['Nombre']); ?></td>
								<td class="number"><?php echo $clasifica ? '<span class="clasifica">Clasifica</span>' : ''; ?></td>
								<td class="number"><?php echo CHtml::encode($dato['Partidos']); ?></td>
								<td class="number optional-stat"><?php echo CHtml::encode($dato['Ganados']); ?></td>
								<td class="number optional-stat"><?php echo CHtml::encode($dato['Empatados']); ?></td>
								<td class="number optional-stat"><?php echo CHtml::encode($dato['Perdidos']); ?></td>
								<td class="number optional-stat"><?php echo CHtml::encode($dato['GolFavor']); ?></td>
								<td class="number optional-stat"><?php echo CHtml::encode($dato['GolContra']); ?></td>
								<td class="number"><?php echo CHtml::encode($dato['Diferencia']); ?></td>
								<td class="points"><?php echo CHtml::encode($dato['Puntos']); ?></td>
							</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php } ?>
</div>

<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
