<h1><?php echo $equipo->Nombre;?></h1>
<table class="table">
<thead>
	<th>Rival</th>
	<th></th>
	<th>Cancha</th>
	<th>Fecha</th>
</thead>
<?php 
	foreach($fixture as $fix){
		echo '<tr>';
		if($fix->Local == $idEquipo){
			echo '<td>' . $fix->visitante->Nombre . '</td>';
			echo '<td>Local</td>';
		}else{
			echo '<td>' . $fix->local->Nombre . '</td>';
			echo '<td>Visitante</td>';
		}
		echo '<td>' . $fix->Cancha->Nombre . '</td>';
		echo '<td>' . $fix->Fecha . '</td>';
		echo '</tr>';
	}
?>
</table>