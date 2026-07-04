<h3>Resultados</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Local</th>
			<th>Gol</th>
			<th>Visitante</th>
			<th>Gol</th>
		</tr>
	</thead>
<?php
foreach ($datos as $dato) {
	?>
	<tr>
        <td><?php echo $dato->local->Nombre;?></a></td>
        <td><?php echo $dato->GolLocal;?></td>
        <td><?php echo $dato->visitante->Nombre;?></a></td>
        <td><?php echo $dato->GolVisitante;?></td>
	</tr>
    <?php /*<tr>
    	<td><?php echo Goles::model()->golPartido($dato->idFixture,$dato->Local, $torneo);?></td>
        <td></td>
        <td><?php echo Goles::model()->golPartido($dato->idFixture,$dato->Visitante, $torneo);?></td>
        <td></td>
    </tr>*/?>
    <tr></tr>
<?php } 
?>
</table>

<?php
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Resultados');
//$xls->addArray($data);
$xls->generateXML('Resultados');
?>