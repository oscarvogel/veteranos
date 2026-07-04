<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'liberarForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<fieldset>
	<legend>Seleccione Torneo</legend>
	
	<div class="row">
		<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo()); ?>
	</div>
	
	<div class="form-actions">
	    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Procesar')); ?>
	</div>
</fieldset>
<?php $this->endWidget();?>
