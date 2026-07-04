<?php
if(isset($jugadores)){?>
	<table class="table">
	<thead>
		<th>Nombre</th>
		<th>DNI</th>
		<th>Clase</th>
		<th>Legajo</th>
	</thead>
    <?php foreach ($jugadores as $jugador) {?>
    <tr>
        <td><?php echo $jugador->Nombre;?></td>
        <td><?php echo $jugador->DNI;?></td>
        <td><?php echo $jugador->Clase;?></td>
        <td><a href="<?php echo Yii::app()->createUrl('jugador/legajo', array('id'=>$jugador->idJugador));?>" class="btn btn-info btn-xs">Legajo</a></td>
    </tr>
    <?php }?>
    </table>
<?php }?>
