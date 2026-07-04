<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'equipostorneo-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idEquipo',Equipos::getListEquipo()); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo()); ?>
	</div>

	<div class="row">
	    <?php echo $form->textFieldRow($model, 'lista');?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->