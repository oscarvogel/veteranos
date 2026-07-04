<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'idTarjeta'); ?>
		<?php echo $form->textField($model,'idTarjeta',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'idFixture'); ?>
		<?php echo $form->textField($model,'idFixture',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'idJugador'); ?>
		<?php echo $form->textField($model,'idJugador',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Amarilla'); ?>
		<?php echo $form->textField($model,'Amarilla'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Roja'); ?>
		<?php echo $form->textField($model,'Roja'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DesdeFecha'); ?>
		<?php echo $form->textField($model,'DesdeFecha',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'HastaFecha'); ?>
		<?php echo $form->textField($model,'HastaFecha'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Motivo'); ?>
		<?php echo $form->textArea($model,'Motivo',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Busqueda')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->