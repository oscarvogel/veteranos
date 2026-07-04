<h4 class="alert alert-info">Goles por Jugador</h4>
<table class="table">
	<thead>
		<th>Equipo al que hizo</th>
		<th>Fecha</th>
		<th>Cantidad</th>
	</thead>

<?php
foreach ($goles as $gol) {?>
	<tr>
		<td><?php echo ($gol->Jugador->idEquipo==$gol->Fixture->Local) ? $gol->Fixture->visitante->Nombre : $gol->Fixture->local->Nombre;?></td>
		<td><?php echo $gol->Fixture->NFecha;?></td>
		<td><?php echo $gol->Cantidad;?></td>
	</tr>
<?php }
?>

</table>