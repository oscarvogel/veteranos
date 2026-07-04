<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'idFixture'); ?>
		<?php echo $form->textField($model,'idFixture',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'idTorneo'); ?>
		<?php echo $form->textField($model,'idTorneo',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'NFecha'); ?>
		<?php echo $form->textField($model,'NFecha',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Fecha'); ?>
		<?php echo $form->textField($model,'Fecha'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Local'); ?>
		<?php echo $form->textField($model,'Local',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Visitante'); ?>
		<?php echo $form->textField($model,'Visitante',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'GolLocal'); ?>
		<?php echo $form->textField($model,'GolLocal',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'GolVisitante'); ?>
		<?php echo $form->textField($model,'GolVisitante',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Busqueda')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->