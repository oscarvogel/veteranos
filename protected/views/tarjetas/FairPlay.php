<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<fieldset>
	<legend>Seleccione Torneo</legend>
	
	<div class="row">
		<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo()); ?>
	</div>
	
	<div class="form-actions">
	    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar')); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('name'=>'btnExcel'))); ?>
	</div>
</fieldset>
<?php $this->endWidget();?>

<br /><?php if(isset($Tarjetas)){?>
	<table class="table">
	<thead>
		<th>Equipo</th>
		<th>Amarillas</th>
		<th>Rojas</th>
	</thead>

<?php
//print_r($Tarjetas);
foreach ($Tarjetas as $tarjeta) {?>
	<tr>
		<td><?php echo $tarjeta->Equipo->Nombre; ?></td>
		<td><?php echo $tarjeta->Amarilla ;?></td>
		<td><?php echo $tarjeta->Roja ;?></td>
	</tr>
<?php }

?>
</table>
<?php }