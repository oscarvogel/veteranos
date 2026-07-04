<?php
$this->breadcrumbs=array('Caja'=>'#','Resumen mensual');
$this->menu=array(
	array('label'=>'Registrar Pago', 'url'=>array('create')),
	array('label'=>'Administrar Recibos', 'url'=>array('admin')),
	array('label'=>'Arqueo de Caja', 'url'=>array('arqueoCaja')),
	array('label'=>'Conceptos de cobro', 'url'=>array('/conceptos/admin')),
);

$meses = array(
	1=>'Enero',
	2=>'Febrero',
	3=>'Marzo',
	4=>'Abril',
	5=>'Mayo',
	6=>'Junio',
	7=>'Julio',
	8=>'Agosto',
	9=>'Septiembre',
	10=>'Octubre',
	11=>'Noviembre',
	12=>'Diciembre',
);
$anios = array();
for($year = (int)date('Y') + 1; $year >= 2013; $year--) {
	$anios[$year] = $year;
}
$kpis = $resumen['kpis'];
$labelsConceptos = array();
$totalesConceptos = array();
foreach($resumen['porConcepto'] as $row) {
	$labelsConceptos[] = $row['Nombre'];
	$totalesConceptos[] = round((float)$row['totalVigente'], 2);
}
$labelsDias = array();
$totalesDias = array();
foreach($resumen['porDia'] as $fecha=>$total) {
	$labelsDias[] = date('d', strtotime($fecha));
	$totalesDias[] = round((float)$total, 2);
}

Yii::app()->clientScript->registerCss('resumen-mensual-caja', '
.resumen-page-header {
	align-items: flex-end;
	display: flex;
	gap: 16px;
	justify-content: space-between;
	margin-bottom: 20px;
}
.resumen-page-header h1 {
	margin-bottom: 6px;
}
.resumen-page-header p {
	color: #64748b;
	margin: 0;
}
.resumen-filter {
	align-items: flex-end;
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	display: flex;
	gap: 10px;
	margin-bottom: 18px;
	padding: 14px;
}
.resumen-filter label {
	color: #334155;
	display: block;
	font-size: 12px;
	font-weight: 700;
	margin-bottom: 5px;
}
.resumen-filter select {
	height: 38px;
	margin: 0;
	min-width: 130px;
}
.resumen-kpis {
	display: grid;
	gap: 14px;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	margin-bottom: 18px;
}
.resumen-kpi {
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	padding: 16px;
}
.resumen-kpi span {
	color: #64748b;
	display: block;
	font-size: 12px;
	font-weight: 700;
	margin-bottom: 8px;
	text-transform: uppercase;
}
.resumen-kpi strong {
	color: #0f172a;
	display: block;
	font-size: 24px;
	line-height: 1.1;
}
.resumen-panels {
	display: grid;
	gap: 16px;
	grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
	margin-bottom: 18px;
}
.resumen-panel {
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	padding: 16px;
}
.resumen-panel h2 {
	font-size: 16px;
	margin: 0 0 12px;
}
.resumen-chart {
	display: block;
	height: 260px;
	width: 100%;
}
.resumen-empty {
	color: #64748b;
	padding: 32px 0;
	text-align: center;
}
.resumen-table {
	background: #fff;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	overflow: hidden;
}
.resumen-table table {
	margin-bottom: 0;
}
.resumen-table th {
	background: #f8fafc;
}
.resumen-table td,
.resumen-table th {
	padding: 10px 12px;
}
.resumen-money {
	text-align: right;
	white-space: nowrap;
}
@media (max-width: 991px) {
	.resumen-kpis,
	.resumen-panels {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
}
@media (max-width: 767px) {
	.resumen-page-header,
	.resumen-filter {
		align-items: stretch;
		flex-direction: column;
	}
	.resumen-kpis,
	.resumen-panels {
		grid-template-columns: 1fr;
	}
	.resumen-chart {
		height: 220px;
	}
}
');

Yii::app()->clientScript->registerScript('resumen-mensual-charts', '
(function(){
	var conceptosLabels = ' . CJSON::encode($labelsConceptos) . ';
	var conceptosValues = ' . CJSON::encode($totalesConceptos) . ';
	var diasLabels = ' . CJSON::encode($labelsDias) . ';
	var diasValues = ' . CJSON::encode($totalesDias) . ';

	function money(value) {
		return "$ " + Number(value || 0).toLocaleString("es-AR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
	}

	function drawBarChart(canvasId, labels, values, color) {
		var canvas = document.getElementById(canvasId);
		if(!canvas || !labels.length) return;
		var rect = canvas.getBoundingClientRect();
		var dpr = window.devicePixelRatio || 1;
		canvas.width = rect.width * dpr;
		canvas.height = rect.height * dpr;
		var ctx = canvas.getContext("2d");
		ctx.scale(dpr, dpr);
		var width = rect.width;
		var height = rect.height;
		var padding = { top: 18, right: 16, bottom: 42, left: 58 };
		var chartWidth = width - padding.left - padding.right;
		var chartHeight = height - padding.top - padding.bottom;
		var max = Math.max.apply(null, values.concat([1]));
		ctx.clearRect(0, 0, width, height);
		ctx.font = "12px Segoe UI, Arial, sans-serif";
		ctx.fillStyle = "#64748b";
		ctx.strokeStyle = "#e2e8f0";
		ctx.lineWidth = 1;
		for(var i = 0; i <= 4; i++) {
			var y = padding.top + chartHeight - (chartHeight * i / 4);
			ctx.beginPath();
			ctx.moveTo(padding.left, y);
			ctx.lineTo(width - padding.right, y);
			ctx.stroke();
			ctx.fillText(money(max * i / 4), 4, y + 4);
		}
		var gap = 8;
		var barWidth = Math.max(10, (chartWidth - gap * (labels.length - 1)) / labels.length);
		for(var j = 0; j < labels.length; j++) {
			var value = Number(values[j] || 0);
			var barHeight = chartHeight * value / max;
			var x = padding.left + j * (barWidth + gap);
			var yBar = padding.top + chartHeight - barHeight;
			ctx.fillStyle = color;
			ctx.fillRect(x, yBar, barWidth, barHeight);
			ctx.fillStyle = "#334155";
			var label = String(labels[j]);
			if(label.length > 12) label = label.substring(0, 11) + "...";
			ctx.save();
			ctx.translate(x + barWidth / 2, height - 12);
			ctx.rotate(-Math.PI / 8);
			ctx.textAlign = "right";
			ctx.fillText(label, 0, 0);
			ctx.restore();
		}
	}

	function drawLineChart(canvasId, labels, values) {
		var canvas = document.getElementById(canvasId);
		if(!canvas || !labels.length) return;
		var rect = canvas.getBoundingClientRect();
		var dpr = window.devicePixelRatio || 1;
		canvas.width = rect.width * dpr;
		canvas.height = rect.height * dpr;
		var ctx = canvas.getContext("2d");
		ctx.scale(dpr, dpr);
		var width = rect.width;
		var height = rect.height;
		var padding = { top: 18, right: 18, bottom: 30, left: 58 };
		var chartWidth = width - padding.left - padding.right;
		var chartHeight = height - padding.top - padding.bottom;
		var max = Math.max.apply(null, values.concat([1]));
		ctx.clearRect(0, 0, width, height);
		ctx.font = "12px Segoe UI, Arial, sans-serif";
		ctx.fillStyle = "#64748b";
		ctx.strokeStyle = "#e2e8f0";
		for(var i = 0; i <= 4; i++) {
			var y = padding.top + chartHeight - (chartHeight * i / 4);
			ctx.beginPath();
			ctx.moveTo(padding.left, y);
			ctx.lineTo(width - padding.right, y);
			ctx.stroke();
			ctx.fillText(money(max * i / 4), 4, y + 4);
		}
		ctx.beginPath();
		for(var j = 0; j < labels.length; j++) {
			var x = padding.left + (labels.length === 1 ? chartWidth / 2 : chartWidth * j / (labels.length - 1));
			var value = Number(values[j] || 0);
			var yPoint = padding.top + chartHeight - (chartHeight * value / max);
			if(j === 0) ctx.moveTo(x, yPoint); else ctx.lineTo(x, yPoint);
		}
		ctx.strokeStyle = "#2563eb";
		ctx.lineWidth = 3;
		ctx.stroke();
		ctx.fillStyle = "#2563eb";
		for(var k = 0; k < labels.length; k++) {
			var xPoint = padding.left + (labels.length === 1 ? chartWidth / 2 : chartWidth * k / (labels.length - 1));
			var yDot = padding.top + chartHeight - (chartHeight * Number(values[k] || 0) / max);
			ctx.beginPath();
			ctx.arc(xPoint, yDot, 3, 0, Math.PI * 2);
			ctx.fill();
			if(k === 0 || k === labels.length - 1 || k % 5 === 0) {
				ctx.fillStyle = "#64748b";
				ctx.textAlign = "center";
				ctx.fillText(labels[k], xPoint, height - 8);
				ctx.fillStyle = "#2563eb";
			}
		}
	}

	function drawCharts() {
		drawBarChart("conceptosChart", conceptosLabels, conceptosValues, "#16a34a");
		drawLineChart("diasChart", diasLabels, diasValues);
	}
	$(drawCharts);
	$(window).on("resize", drawCharts);
})();
', CClientScript::POS_END);
?>

<div class="resumen-page-header">
	<div>
		<h1>Resumen mensual de caja</h1>
		<p><?php echo CHtml::encode($meses[$mes] . ' ' . $anio); ?>, agrupado por concepto de cobro.</p>
	</div>
</div>

<form method="get" class="resumen-filter">
	<input type="hidden" name="r" value="ingresos/resumenMensual">
	<div>
		<label>Mes</label>
		<?php echo CHtml::dropDownList('mes', $mes, $meses); ?>
	</div>
	<div>
		<label>Año</label>
		<?php echo CHtml::dropDownList('anio', $anio, $anios); ?>
	</div>
	<button type="submit" class="btn btn-primary">Consultar</button>
</form>

<div class="resumen-kpis">
	<div class="resumen-kpi">
		<span>Total cobrado</span>
		<strong>$ <?php echo number_format((float)$kpis['totalVigente'], 2, ',', '.'); ?></strong>
	</div>
	<div class="resumen-kpi">
		<span>Recibos vigentes</span>
		<strong><?php echo (int)$kpis['cantidadVigente']; ?></strong>
	</div>
	<div class="resumen-kpi">
		<span>Promedio por recibo</span>
		<strong>$ <?php echo number_format((float)$kpis['promedioVigente'], 2, ',', '.'); ?></strong>
	</div>
	<div class="resumen-kpi">
		<span>Anulados</span>
		<strong>$ <?php echo number_format((float)$kpis['totalAnulado'], 2, ',', '.'); ?></strong>
	</div>
</div>

<div class="resumen-panels">
	<div class="resumen-panel">
		<h2>Cobrado por concepto</h2>
		<?php if(count($labelsConceptos) > 0): ?>
			<canvas id="conceptosChart" class="resumen-chart"></canvas>
		<?php else: ?>
			<div class="resumen-empty">No hay cobranzas para este periodo.</div>
		<?php endif; ?>
	</div>
	<div class="resumen-panel">
		<h2>Evolucion diaria</h2>
		<canvas id="diasChart" class="resumen-chart"></canvas>
	</div>
</div>

<div class="resumen-table">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Concepto</th>
				<th class="resumen-money">Recibos</th>
				<th class="resumen-money">Vigentes</th>
				<th class="resumen-money">Anulados</th>
				<th class="resumen-money">Total vigente</th>
				<th class="resumen-money">Total anulado</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($resumen['porConcepto']) === 0): ?>
				<tr><td colspan="6" class="resumen-empty">No hay datos para mostrar.</td></tr>
			<?php endif; ?>
			<?php foreach($resumen['porConcepto'] as $row): ?>
				<tr>
					<td><?php echo CHtml::encode($row['Nombre']); ?></td>
					<td class="resumen-money"><?php echo (int)$row['cantidadRecibos']; ?></td>
					<td class="resumen-money"><?php echo (int)$row['cantidadVigente']; ?></td>
					<td class="resumen-money"><?php echo (int)$row['cantidadAnulada']; ?></td>
					<td class="resumen-money">$ <?php echo number_format((float)$row['totalVigente'], 2, ',', '.'); ?></td>
					<td class="resumen-money">$ <?php echo number_format((float)$row['totalAnulado'], 2, ',', '.'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
