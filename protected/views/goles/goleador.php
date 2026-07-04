<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'inline',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="row">
	<?php echo $form->dropDownListRow($torneo,'idTorneo',Torneo::getListTorneo('I'),
                                     array('class'=>'form-control')); ?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar', 'htmlOptions'=>array('class' => 'button primary'))); ?>
   	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('class' => 'button success','name'=>'btnExcel'))); ?>
</div>
<?php $this->endWidget();?>

<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Goles</th>
        <th></th>
	</thead>
<?php
if(isset($goleador)){
	foreach ($goleador as $gol) {;?>
		<tr>
			<td><?php echo $gol->Jugador->Nombre;?></td>
			<td><?php echo $gol->Jugador->Equipo->Nombre;?></td>
			<td><?php echo $gol->Cantidad;?></td>
			<td><a class="btn" href="<?php echo Yii::app()->createUrl('goles/GolesJugador',array('idJugador'=>$gol->idJugador,'idTorneo'=>$idTorneo));?>"/>Ver Goles</a></td>
		</tr>
	<?php }
}?>
</table>