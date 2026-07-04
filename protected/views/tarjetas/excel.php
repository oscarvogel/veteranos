<p>Tarjetas Rojas</p>
<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
	</thead>

<?php
//print_r($modelAmarillas);
foreach ($modelRojas as $tarjeta) {?>
	<tr class="alert alert-error">
		<td><?php echo $tarjeta->Jugador->Nombre;?></a></td>
		<td><?php echo $tarjeta->Jugador->Equipo->Nombre;?></td>
	</tr>
<?php }

?>
</table>

<p>Tarjetas Amarillas</p>
<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Cantidad</th>
	</thead>

<?php
//print_r($modelAmarillas);
foreach ($modelAmarillas as $tarjeta) {?>
	<tr>
		<td><?php echo $tarjeta->Jugador->Nombre;?></a></td>
		<td><?php echo $tarjeta->Jugador->Equipo->Nombre;?></td>
		<td><?php echo $tarjeta->Amarilla ;?></td>
	</tr>
<?php }

?>
</table>

<?php 
$data = array(
    1 => array ('Name', 'Surname'),
    array('Schwarz', 'Oliver'),
    array('Test', 'Peter')
);
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Tarjetas');
//$xls->addArray($data);
$xls->generateXML('Tarjetas');
?>
