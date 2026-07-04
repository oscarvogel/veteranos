<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Amarilla</th>
		<th>Roja</th>
		<th>Desde Fecha</th>
		<th>Hasta Fecha</th>
		<th>Motivo</th>
	</thead>
<?php
//print_r($model);
foreach ($model as $tarjeta) {?>
	<tr class="<?php echo ($tarjeta->Amarilla) ? "warning" : "error"; ?>">
		<td><?php echo $tarjeta->Jugador->Nombre;?></td>
		<td><?php echo ($tarjeta->Amarilla == 1) ? "SI" : "NO";?></td>
		<td><?php echo ($tarjeta->Roja == 1) ? "SI" : "NO";?></td>
		<td><?php echo $tarjeta->DesdeFecha;?></td>
		<td><?php echo $tarjeta->HastaFecha;?></td>
		<td><?php echo $tarjeta->Motivo;?></td>
	</tr>
<?php }

?>

</table>

<?php echo CHtml::link('Agregar Tarjeta',array('tarjetas/create',
		'idFixture'=>$idFixture,
		'idEquipo'=>$idEquipo,
	),array('class'=>'btn btn-large')); ?>