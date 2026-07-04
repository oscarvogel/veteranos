<table class="table">
	<thead>
		<th>Fecha</th>
		<th>Detalle</th>
		<?php if($tipo=="I") 
			echo '<th>Equipo</th>';?>
		<th>Monto</th>
	</thead>
<?php
foreach ($datos as $dato) {?>
	<tr>
		<td><?php echo $dato->Fecha;?></td>
		<td><?php echo $dato->Detalle;?></td>
		<?php if($tipo=="I")
			echo '<td>' . $dato->Equipos->Nombre . '</td>';?>
		<td><?php echo $dato->Monto;?></td>
	</tr>
<?php }
?>
</table>