<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Goles</th>
	</thead>
<?php
if(isset($goleador)){
	foreach ($goleador as $gol) {;?>
		<tr>
			<td><?php echo htmlentities($gol->Jugador->Nombre, ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo htmlentities($gol->Jugador->Equipo->Nombre, ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo $gol->Cantidad;?></td>
		</tr>
	<?php }
}?>
</table>

<?php
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Goleadores');
//$xls->addArray($data);
$xls->generateXML('Goleadores');
?>