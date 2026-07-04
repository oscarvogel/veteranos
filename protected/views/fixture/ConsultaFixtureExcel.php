<?php if(isset($partidos)){?>
	<table class="table table-bordered">
		<thead>
			<th>Nº Fecha</th>
			<th>Local</th>
			<th>Visitante</th>
		</thead>
		<?php 
			$fecha = 0;
			foreach ($partidos as $partido) {?>
			<tr class="<?php echo ($partido->Visitante==0) ? 'success' : '' ?>">
				<td><?php echo ($partido->NFecha == $fecha) ? '' : $partido->NFecha; $fecha = $partido->NFecha;?></td>
				<td><?php echo $partido->local->Nombre;?></td>
				<td><?php echo $partido->visitante->Nombre;?></td>
			</tr>
		<?php }?>
	</table>
<?php }?>

<?php
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'FixtureCompleto');
//$xls->addArray($data);
$xls->generateXML('FixtureCompleto');
?>