<?php if(isset($Tarjetas)){?>
	<table class="table">
	<thead>
		<th>Equipo</th>
		<th>Amarillas</th>
		<th>Rojas</th>
	</thead>

<?php
//print_r($Tarjetas);
foreach ($Tarjetas as $tarjeta) {?>
	<tr>
		<td><?php echo $tarjeta->Equipo->Nombre; ?></td>
		<td><?php echo $tarjeta->Amarilla ;?></td>
		<td><?php echo $tarjeta->Roja ;?></td>
	</tr>
<?php }

?>
</table>
<?php 
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'FairPlay');
//$xls->addArray($data);
$xls->generateXML('Tarjetas');
}
