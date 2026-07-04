<?php //Yii::app()->bootstrap->register();?>


<h4 class="alert alert-info">Goles</h4>
<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Cantidad</th>
	</thead>
<?php
foreach ($goles as $gol) {?>
	<tr>
		<td><?php echo $gol->Jugador->Nombre;?></td>
		<td><?php echo $gol->Jugador->Equipo->Nombre;?></td>
		<td><?php echo $gol->Cantidad;?></td>
	</tr>
<?php }
?>
</table>

<h4 class="alert alert-info">Tarjetas</h4>
<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Amarilla</th>
		<th>Roja</th>
		<th>Desde Fecha</th>
		<th>Hasta Fecha</th>
		<th>Motivo</th>
        <th></th>
	</thead>
<?php
//print_r($model);
foreach ($tarjetas as $tarjeta) {?>
	<tr class="<?php echo ($tarjeta->Amarilla) ? "warning" : "error"; ?>">
		<td><?php echo $tarjeta->Jugador->Nombre;?></td>
		<td><?php echo ($tarjeta->Amarilla) ? "SI" : "NO";?></td>
		<td><?php echo ($tarjeta->Roja) ? "SI" : "NO";?></td>
		<td><?php echo $tarjeta->DesdeFecha;?></td>
		<td><?php echo $tarjeta->HastaFecha;?></td>
		<td><?php echo $tarjeta->Motivo;?></td>
        <td><?php 
                if(!Yii::app()->user->isGuest){
                    echo CHtml::link(CHtml::encode('Borra Tarjeta'),
                            array('tarjetas/delete', 'id'=>$tarjeta->idTarjeta),
                                array(
                                    'submit'=>array('tarjetas/delete', 'id'=>$tarjeta->idTarjeta),
                                    'class' => 'icon-trash','confirm'=>'Esto borrará la tarjeta. Estás seguro?'
                                    )) ;
                }
                ?>
        </td>
	</tr>
<?php }
?>

</table>

<?php 
if(!Yii::app()->user->isGuest){ //si es un usuario registrado muestra el boton para agregar tarjetas
	?>
	<?php /*
	echo CHtml::link('Agregar Tarjeta', Yii::app()->createUrl(array('/tarjetas/update',
			'model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
			'torneo'=>$torneo,)
		));*/
	echo CHtml::link('Agregar Tarjeta',array('tarjetas/create',
            	'idFixture'=>$idFixture,
             	'idEquipo'=>$idEquipo,),
	 		array('target'=>'_blank', 'class'=>'btn btn-primary'));
	?>
	<?php /*$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); 
		echo $this->renderPartial('//tarjetas/_form',array(
			'model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
			'torneo'=>$torneo,
		));	
	
	$this->endWidget(); */
}?>