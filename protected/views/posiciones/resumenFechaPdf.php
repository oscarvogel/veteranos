<?php
$nombreTorneo = $torneo ? $torneo->Nombre : $idTorneo;
$fechaMostrar = date('d/m/Y', strtotime($fecha));
$totalPartidos = count($resultados);
$golesLocales = 0;
$golesVisitantes = 0;
foreach($resultados as $partido){
	$golesLocales += (int)$partido->GolLocal;
	$golesVisitantes += (int)$partido->GolVisitante;
}
$totalGoles = $golesLocales + $golesVisitantes;
$promedioGoles = $totalPartidos > 0 ? number_format($totalGoles / $totalPartidos, 2, ',', '.') : '0,00';
$totalAmarillas = isset($totalAmarillasFecha) ? (int)$totalAmarillasFecha : 0;
?>
<style>
	.resumen-fecha,.resumen-fecha table,.resumen-fecha td,.resumen-fecha th,.resumen-fecha div{font-family:DejaVu Sans, Arial, sans-serif}
	.resumen-fecha{font-size:9px;color:#071b39;background-color:#fff}
	.resumen-fecha .masthead{width:100%;border-collapse:collapse;margin-bottom:10px;background-color:#071b39}
	.resumen-fecha .masthead td{border:0;padding:12px 14px;vertical-align:middle;color:#fff;background-color:#071b39}
	.resumen-fecha .brand-cell{width:92px}
	.resumen-fecha .shield{width:68px;height:80px;border:2px solid #fff;background-color:#006b3f;color:#fff;text-align:center;font-weight:bold}
	.resumen-fecha .shield-main{font-size:28px;line-height:31px;padding-top:13px}
	.resumen-fecha .shield-year{font-size:10px;letter-spacing:1px}
	.resumen-fecha .title-cell{padding-left:12px!important}
	.resumen-fecha .report-title{font-size:28px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#fff;line-height:34px}
	.resumen-fecha .report-subtitle{font-size:14px;letter-spacing:2px;text-transform:uppercase;color:#f6c846;margin-top:2px}
	.resumen-fecha .meta-table{width:100%;border-collapse:collapse}
	.resumen-fecha .meta-table td{border-bottom:1px solid #55708f;padding:6px 0;font-size:9px;color:#fff;background-color:#071b39}
	.resumen-fecha .meta-label{font-weight:bold;text-transform:uppercase;color:#f6c846;width:64px}
	.resumen-fecha .kpi-table{width:100%;border-collapse:collapse;margin:8px 0 12px 0}
	.resumen-fecha .kpi-table td{border-left:1px solid #b9c3d0;padding:6px 8px;text-align:center;vertical-align:middle}
	.resumen-fecha .kpi-table td:first-child{border-left:0}
	.resumen-fecha .kpi-value{font-size:19px;font-weight:bold;color:#071b39;line-height:20px}
	.resumen-fecha .kpi-label{font-size:8px;text-transform:uppercase;color:#384a60;letter-spacing:.4px;margin-top:2px}
	.resumen-fecha .kpi-icon{font-size:15px;color:#006b3f;font-weight:bold}
	.resumen-fecha .section-grid{width:100%;border-collapse:collapse;margin-top:3px}
	.resumen-fecha .section-grid>tbody>tr>td{border:0;padding:0;vertical-align:top}
	.resumen-fecha .left-col{width:43%;padding-right:8px!important}
	.resumen-fecha .right-col{width:57%;padding-left:8px!important}
	.resumen-fecha .title-table{width:100%;border-collapse:collapse;margin:0}
	.resumen-fecha .title-table td{border:0;background-color:#071b39;color:#f6c846;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.8px;padding:7px 9px}
	.resumen-fecha .qualify-table{width:100%;border-collapse:collapse;margin:3px 0 0 0}
	.resumen-fecha .qualify-table td{border:0;background-color:#006b3f;color:#fff;text-align:center;font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;padding:6px 8px}
	.resumen-fecha table.data{border-collapse:collapse;width:100%;margin-bottom:9px}
	.resumen-fecha table.data th{background-color:#e9eef5;color:#20344f;border-bottom:1px solid #aeb9c7;padding:5px 4px;text-transform:uppercase;font-size:8px}
	.resumen-fecha table.data td{border-bottom:1px solid #cbd4df;padding:5px 4px}
	.resumen-fecha .text-center{text-align:center}
	.resumen-fecha .text-right{text-align:right}
	.resumen-fecha .team-name{font-weight:bold;color:#071b39}
	.resumen-fecha .score{background-color:#006b3f;color:#fff;font-weight:bold;font-size:13px;text-align:center;white-space:nowrap;padding:4px 6px}
	.resumen-fecha .score-empty{background-color:#eef2f6;color:#071b39}
	.resumen-fecha .result-table{margin-bottom:0}
	.resumen-fecha .result-table tbody td{height:39px;padding:8px 5px;vertical-align:middle;font-size:9.5px}
	.resumen-fecha .result-table .score{font-size:14px;padding:8px 6px}
	.resumen-fecha .result-note{border-top:1px solid #cbd4df;padding-top:8px;line-height:15px}
	.resumen-fecha .pos-table td{font-size:8.5px;padding:4px 3px!important}
	.resumen-fecha .pos-table th{font-size:7.5px;padding:4px 3px!important}
	.resumen-fecha .clasificado td{background-color:#e8f4ed}
	.resumen-fecha .rank{font-weight:bold;color:#006b3f;font-size:11px!important}
	.resumen-fecha .badge{display:inline-block;font-size:7px;font-weight:bold;text-transform:uppercase;color:#006b3f;background-color:#d9efe3;border:1px solid #7fb796;padding:2px 4px}
	.resumen-fecha .pts{font-weight:bold;background-color:#eef5ff;color:#071b39;font-size:10px!important}
	.resumen-fecha .legend{border-top:1px solid #8f9bae;border-bottom:1px solid #8f9bae;padding:6px 0;margin:4px 0 10px 0;font-size:8px;color:#20344f;text-align:center}
	.resumen-fecha .split-table{width:100%;border-collapse:collapse;page-break-inside:avoid}
	.resumen-fecha .split-table>tbody>tr>td{border:0;vertical-align:top;padding:0}
	.resumen-fecha .split-left{width:50%;padding-right:8px!important}
	.resumen-fecha .split-right{width:50%;padding-left:8px!important}
	.resumen-fecha .note-table{width:100%;border-collapse:collapse;margin-top:-9px;margin-bottom:9px}
	.resumen-fecha .note-table td{border:0;background-color:#071b39;color:#fff;font-size:8px;padding:6px 8px}
	.resumen-fecha .empty{color:#667085;font-style:italic}
	.resumen-fecha .muted{color:#52657d}
</style>

<div class="resumen-fecha">
	<table class="masthead">
		<tr>
			<td class="brand-cell">
				<div class="shield">
					<div class="shield-main">SV</div>
					<div class="shield-year">2026</div>
				</div>
			</td>
			<td class="title-cell">
				<div class="report-title"><?php echo CHtml::encode($nombreTorneo); ?></div>
				<div class="report-subtitle">Resumen oficial de fecha</div>
			</td>
			<td style="width:250px">
				<table class="meta-table">
					<tr>
						<td class="meta-label">Fecha</td>
						<td><?php echo CHtml::encode($fechaMostrar); ?></td>
					</tr>
					<tr>
						<td class="meta-label">Torneo</td>
						<td><?php echo CHtml::encode($nombreTorneo); ?></td>
					</tr>
					<tr>
						<td class="meta-label">Liga</td>
						<td>Futbol Veteranos</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table class="kpi-table">
		<tr>
			<td><div class="kpi-icon">#</div><div class="kpi-value"><?php echo $totalPartidos; ?></div><div class="kpi-label">Partidos</div></td>
			<td><div class="kpi-icon">G</div><div class="kpi-value"><?php echo $totalGoles; ?></div><div class="kpi-label">Goles</div></td>
			<td><div class="kpi-icon">%</div><div class="kpi-value"><?php echo $promedioGoles; ?></div><div class="kpi-label">Prom. goles</div></td>
			<td><div class="kpi-icon">L</div><div class="kpi-value"><?php echo $golesLocales; ?></div><div class="kpi-label">Goles locales</div></td>
			<td><div class="kpi-icon">V</div><div class="kpi-value"><?php echo $golesVisitantes; ?></div><div class="kpi-label">Goles visitantes</div></td>
			<td><div class="kpi-icon">T</div><div class="kpi-value"><?php echo $totalAmarillas; ?></div><div class="kpi-label">Amarillas</div></td>
		</tr>
	</table>

	<table class="section-grid">
		<tr>
			<td class="left-col">
				<table class="title-table"><tr><td>Resultados</td></tr></table>
				<table class="data result-table">
					<thead>
						<tr>
							<th>Local</th>
							<th class="text-center">Resultado</th>
							<th>Visitante</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($resultados) === 0){ ?>
							<tr><td class="empty" colspan="3">Sin partidos para la fecha seleccionada.</td></tr>
						<?php } ?>
						<?php foreach($resultados as $partido){
							$local = $partido->local !== null ? $partido->local->Nombre : 'Libre';
							$visitante = $partido->visitante !== null ? $partido->visitante->Nombre : 'Libre';
						?>
							<tr>
								<td class="team-name"><?php echo CHtml::encode($local); ?></td>
								<td class="score"><?php echo CHtml::encode($partido->GolLocal); ?> - <?php echo CHtml::encode($partido->GolVisitante); ?></td>
								<td class="team-name"><?php echo CHtml::encode($visitante); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<div class="muted result-note">Resultados al <?php echo CHtml::encode($fechaMostrar); ?>. Incluye todos los partidos de la fecha seleccionada.</div>
			</td>
			<td class="right-col">
				<table class="title-table"><tr><td>Tabla de Posiciones</td></tr></table>
				<table class="qualify-table"><tr><td>Clasifican los primeros 8 equipos</td></tr></table>
				<table class="data pos-table">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th>Equipo</th>
							<th class="text-center">Estado</th>
							<th class="text-center">PJ</th>
							<th class="text-center">PG</th>
							<th class="text-center">PE</th>
							<th class="text-center">PP</th>
							<th class="text-center">GF</th>
							<th class="text-center">GC</th>
							<th class="text-center">Dif</th>
							<th class="text-center">Pts</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($posiciones) === 0){ ?>
							<tr><td class="empty" colspan="11">Sin posiciones para el torneo seleccionado.</td></tr>
						<?php } ?>
						<?php $i = 1; foreach($posiciones as $dato){
							$clasifica = $i <= 8;
						?>
							<tr<?php echo $clasifica ? ' class="clasificado"' : ''; ?>>
								<td class="text-center rank"><?php echo $i; ?></td>
								<td class="team-name"><?php echo CHtml::encode($dato['Nombre']); ?></td>
								<td class="text-center"><?php echo $clasifica ? '<span class="badge">Clasifica</span>' : ''; ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['Partidos']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['Ganados']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['Empatados']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['Perdidos']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['GolFavor']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['GolContra']); ?></td>
								<td class="text-center"><?php echo CHtml::encode($dato['Diferencia']); ?></td>
								<td class="text-center pts"><?php echo CHtml::encode($dato['Puntos']); ?></td>
							</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>

	<div class="legend">
		PJ: Partidos jugados &nbsp; | &nbsp; PG: Ganados &nbsp; | &nbsp; PE: Empatados &nbsp; | &nbsp; PP: Perdidos &nbsp; | &nbsp; GF: Goles a favor &nbsp; | &nbsp; GC: Goles en contra &nbsp; | &nbsp; Dif: Diferencia &nbsp; | &nbsp; Pts: Puntos
	</div>

	<table class="split-table">
		<tr>
			<td class="split-left">
				<table class="title-table"><tr><td>Goleadores</td></tr></table>
				<table class="data">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th>Jugador</th>
							<th>Equipo</th>
							<th class="text-center">Goles</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($goleadores) === 0){ ?>
							<tr><td class="empty" colspan="4">Sin goleadores cargados.</td></tr>
						<?php } ?>
						<?php $i = 1; foreach($goleadores as $gol){ ?>
							<tr>
								<td class="text-center"><?php echo $i; ?></td>
								<td class="team-name"><?php echo CHtml::encode($gol->Jugador ? $gol->Jugador->Nombre : ''); ?></td>
								<td><?php echo CHtml::encode(($gol->Jugador && $gol->Jugador->Equipo) ? $gol->Jugador->Equipo->Nombre : ''); ?></td>
								<td class="text-center pts"><?php echo CHtml::encode($gol->Cantidad); ?></td>
							</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
				<table class="note-table"><tr><td>Top 10 de goleadores del torneo activo.</td></tr></table>
			</td>
			<td class="split-right">
				<table class="title-table"><tr><td>Tarjetas Amarillas</td></tr></table>
				<table class="data">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th>Jugador</th>
							<th>Equipo</th>
							<th class="text-center">Cant.</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($tarjetasAmarillas) === 0){ ?>
							<tr><td class="empty" colspan="4">Sin amarillas para la fecha seleccionada.</td></tr>
						<?php } ?>
						<?php $i = 1; foreach($tarjetasAmarillas as $tarjeta){ ?>
							<tr>
								<td class="text-center"><?php echo $i; ?></td>
								<td class="team-name"><?php echo CHtml::encode($tarjeta->Jugador ? $tarjeta->Jugador->Nombre : ''); ?></td>
								<td><?php echo CHtml::encode(($tarjeta->Jugador && $tarjeta->Jugador->Equipo) ? $tarjeta->Jugador->Equipo->Nombre : ''); ?></td>
								<td class="text-center pts"><?php echo CHtml::encode($tarjeta->Amarilla); ?></td>
							</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
				<table class="note-table"><tr><td>Top 10 de amarillas acumuladas del campeonato.</td></tr></table>
			</td>
		</tr>
	</table>
</div>
