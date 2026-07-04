<table class="table">
	<thead>
		<tr>
			<th>Posicion</th>
			<th>Nombre</th>
			<th>PJ</th>
			<th>PG</th>
			<th>PE</th>
			<th>PP</th>
			<th>GF</th>
			<th>GC</th>
			<th>Dif.</th>
			<th>Puntos</th>
		</tr>
	</thead>
<?php
	//print_r($datos);
	if(!empty($datos)){
		$i = 1;
		foreach ($datos as $dato) {?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $dato['Nombre'];?></td>
				<td><?php echo $dato['Partidos'];?></td>
				<td><?php echo $dato['Ganados'];?></td>
				<td><?php echo $dato['Empatados'];?></td>
				<td><?php echo $dato['Perdidos'];?></td>
				<td><?php echo $dato['GolFavor'];?></td>
				<td><?php echo $dato['GolContra'];?></td>
				<td><?php echo $dato['Diferencia'];?></td>
				<td><?php echo $dato['Puntos'];?></td>
			</tr>
		<?php
		$i++; 
		}
	}
?>
</table>

<?php
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Posiciones');
//$xls->addArray($data);
$xls->generateXML('Posiciones');
?>