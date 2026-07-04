<?php
if(isset($jugadores)){?>
	<h5>ASOCIACION DE FUTBOL DE VETERANOS, SUPER Y SENIOR "LDOR GRAL SAN MARTIN"</h5>
	<table class="table">
		<thead>
			<th>Nº</th>
			<th>Ing</th>
			<th>Nombre</th>
			<th>Clase</th>
			<th>DNI</th>
			<th>Observaciones</th>
		</thead>
	<?php
	foreach ($jugadores as $jugador) {?>

		<tr>
			<td></td>
			<td></td>
			<td><?php echo htmlentities($jugador->Nombre, ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo CHtml::encode($jugador->Clase);?></td>
			<td><?php echo CHtml::encode($jugador->DNI);?></td>
			<td><?php echo CHtml::encode($jugador->Observacion);?></td>
		</tr>
			
	
	<?php }?>
	</table>
<?php 

}
?>
