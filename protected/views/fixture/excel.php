<?php if(isset($partidos)){?>
	<h5>Asociacion de Futbol de Veteranos "Libertador General San Martin"</h5>
	<table class="table table-bordered">
		<thead>
			<th><?php echo htmlentities('Fecha Nº ' . $fixture->NFecha, ENT_QUOTES,'UTF-8');?></th>
			<th></th>
			<th><?php echo htmlentities('Día', ENT_QUOTES,'UTF-8');?></th>
			<th><?php echo Yii::app()->dateFormatter->formatDateTime(CDateTimeParser::parse($fixture->Fecha, 'yyyy-MM-dd'),'short',null);?></th>
 			<th></th>
		</thead>
		<?php $i = 0;
			$idCancha = 0;
			foreach ($partidos as $partido) {
				if($partido->Visitante < 1){?>
					<tr>
						<td><?php echo $partido->local->Nombre;?></td>
						<td><?php echo $partido->visitante->Nombre;?></td>
					</tr>
				<?php }else{
					if($idCancha != $partido->idCancha){
						if($idCancha != 0){
						?>
						<tr>
							<td>Arbitro 1 Partido</td>
							<td><?php echo $Linea1;?></td>
						</tr>
						<tr>
							<td>Arbitro 2 Partido</td>
							<td><?php echo $Arbitro;?></td>
							<td></td>
							<td><?php echo 'Linea: ' . $Linea2;?></td>
						</tr>
					<?php	}
						$i = 0;
					}
					if($i==0){?>
						<tr></tr>
						<tr>
							<td><?php echo 'Cancha: ' . ($partido->idCancha != 1) ? htmlentities($partido->Cancha->Nombre, ENT_QUOTES,'UTF-8') : '';?></td>
						</tr>
						<tr>
							<td><?php echo $partido->local->Nombre;?></td>
							<td>Vs.</td>
							<td><?php echo $partido->visitante->Nombre;?></td>
                            <td><?php echo $partido->Hora;?></td>
						</tr>
					<?php }elseif($i==1){ ?>
						<tr>
							<td><?php echo $partido->local->Nombre;?></td>
							<td>Vs.</td>
							<td><?php echo $partido->visitante->Nombre;?></td>
                            <td><?php echo $partido->Hora;?></td>
						</tr>
					<?php }
					if($i == 1){
						$i = 0;
					}else{
						$i ++;
					}
					$Linea1 = $partido->Linea1->Nombre;
					$Linea2 = $partido->Linea2->Nombre;
					$Arbitro = $partido->Arbitro->Nombre;
					$idCancha = $partido->idCancha;
				}
				?>
		<?php }
		echo '<tr><td>Arbitro 1 Partido</td><td>' . $Linea1 . '</td><tr>';
		echo '<tr><td>Arbitro 2 Partido</td><td>' . $Arbitro . '</td>';
		echo '<td></td><td>' . $Linea2 . '</td><tr>';
		?>
	</table>

<?php 

Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Jugadores');
//$xls->addArray($data);
$xls->generateXML('Fecha');


?>

<?php 
}?>

