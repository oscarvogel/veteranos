<?php
$numero = str_pad($model->NumeroRecibo ? $model->NumeroRecibo : $model->idIngreso, 8, '0', STR_PAD_LEFT);
$firmaPath = dirname(Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'firmas' . DIRECTORY_SEPARATOR . 'firma-recibo-mini.jpg';
?>
<html>
<head>
	<style>
		@page { margin: 8mm 10mm 8mm 10mm; }
		body { font-family: sans-serif; font-size: 10pt; color: #222; }
		.titulo { font-size: 15pt; font-weight: bold; text-align: center; }
		.subtitulo { text-align: center; margin-bottom: 8px; }
		.numero { font-size: 12pt; text-align: right; margin-bottom: 10px; }
		table { width: 100%; border-collapse: collapse; }
		td { padding: 4px 6px; border-bottom: 1px solid #ddd; line-height: 1.15; }
		td:first-child { width: 32%; font-weight: bold; }
		.monto { font-size: 13pt; font-weight: bold; }
		.estado-anulado { color: #900; font-weight: bold; text-align: center; border: 2px solid #900; padding: 8px; }
		.firma { margin-top: 8px; text-align: right; }
		.firma img { width: 45px; height: auto; margin-bottom: -1px; }
		.firma-linea { border-top: 1px solid #555; display: inline-block; padding-top: 2px; text-align: center; width: 26mm; font-size: 8pt; }
	</style>
</head>
<body>
	<div class="titulo"><?php echo CHtml::encode(Yii::app()->params['Sistema']); ?></div>
	<div class="subtitulo">Comprobante de pago</div>
	<div class="numero">Recibo Nro. <?php echo $numero; ?></div>
	<?php if($model->Estado === 'ANULADO'): ?>
		<p class="estado-anulado">RECIBO ANULADO</p>
	<?php endif; ?>
	<table>
		<tr><td>Fecha</td><td><?php echo CHtml::encode($model->Fecha); ?> <?php echo CHtml::encode($model->Hora); ?></td></tr>
		<tr><td>Equipo</td><td><?php echo CHtml::encode($model->Equipos->Nombre); ?></td></tr>
		<tr><td>Concepto</td><td><?php echo CHtml::encode($model->Conceptos->Nombre); ?></td></tr>
		<tr><td>Detalle</td><td><?php echo CHtml::encode($model->Detalle); ?></td></tr>
		<tr><td>Monto</td><td class="monto">$ <?php echo number_format((float)$model->Monto, 2, ',', '.'); ?></td></tr>
		<tr><td>Cobrador</td><td>Tesoreria Asociacion</td></tr>
	</table>
	<div class="firma">
		<?php if(is_file($firmaPath)): ?>
			<img src="<?php echo CHtml::encode($firmaPath); ?>" width="45" alt="Firma">
		<?php endif; ?>
		<br>
		<span class="firma-linea">Firma y aclaracion</span>
	</div>
</body>
</html>
