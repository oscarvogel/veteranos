<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'goles-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->hiddenField($model,'idFixture',array('size'=>10,'maxlength'=>10, 'value'=>$idFixture)); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idJugador',Jugador::getListJugador($idEquipo, $idEquipo)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Cantidad',array('type'=>'number')); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->