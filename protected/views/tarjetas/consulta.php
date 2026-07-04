<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'consultaForm',
    'type'=>'inline',
    'htmlOptions'=>array('class'=>'well'),
)); ?>
<fieldset>
    <legend>Seleccione el torneo a consultar</legend>
    <div class="row">
		<?php echo $form->dropDownListRow($model, 'idTorneo', Torneo::getListTorneo('I'),
                                          array('class'=>'form-control'));?>
    </div>
    <div class="row">
		<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$model,
				'valor'=>'Fecha',
			)); 
		?>
	</div>

</fieldset>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar', 'htmlOptions'=>array('class' => 'button primary'))); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('class' => 'button success','name'=>'btnExcel'))); ?>
</div>
<?php $this->endWidget(); ?>
<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Desde Fecha</th>
		<th>Hasta Fecha</th>
		<th>Motivo Fecha</th>
	</thead>

<?php
//print_r($modelAmarillas);
foreach ($modelRojas as $tarjeta) {?>
	<tr class="alert alert-error">
		<td><a href="<?php echo Yii::app()->createUrl('tarjetas/verJugadorFecha', array('idJugador'=>$tarjeta->idJugador, 'roja'=>TRUE)); ?>"/><?php echo $tarjeta->Jugador->Nombre;?></a></td>
		<td><?php echo $tarjeta->Jugador->Equipo->Nombre;?></td>
		<td><?php echo $tarjeta->DesdeFecha ;?></td>
		<td><?php echo $tarjeta->HastaFecha ;?></td>
		<td><?php echo $tarjeta->Motivo ;?></td>
	</tr>
<?php }

?>
</table>

<table class="table">
	<thead>
		<th>Jugador</th>
		<th>Equipo</th>
		<th>Cantidad</th>
	</thead>

<?php
//print_r($modelAmarillas);
foreach ($modelAmarillas as $tarjeta) {?>
	<tr class="alert alert-warning">
		<td><a href="<?php echo Yii::app()->createUrl('tarjetas/verJugadorFecha', array('idJugador'=>$tarjeta->idJugador, 'amarilla'=>TRUE)); ?>"/><?php echo $tarjeta->Jugador->Nombre;?></a></td>
		<td><?php echo $tarjeta->Jugador->Equipo->Nombre;?></td>
		<td><?php echo $tarjeta->Amarilla ;?></td>
	</tr>
<?php }

?>
</table>