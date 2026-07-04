
<table class="table">
	<thead>
		<th>Nº Fecha</th>
        <th>Fecha</th>
		<th>Contra Equipo</th>
	</thead>
<?php
//echo '<h4>Consulta de tarjetas de ' . $model->Jugador->Nombre . '</h4>';
foreach ($model as $tarjeta) {?>
	<tr>
		<td><?php echo $tarjeta->Fixture->NFecha;?></td>
        <td><?php echo $tarjeta->Fixture->Fecha;?></td>
		<?php if($tarjeta->Fixture->Local == $tarjeta->Jugador->idEquipo){?>
			<td><?php echo $tarjeta->Fixture->visitante->Nombre;?></td>
		<?php }else{?>
			<td><?php echo $tarjeta->Fixture->local->Nombre;?></td>
		<?php } ?>
	</tr>
	
<?php }
?>

</table>