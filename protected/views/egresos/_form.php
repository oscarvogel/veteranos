<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'egresos-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idConcepto',Conceptos::getListConceptos()); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Detalle',array('size'=>60,'maxlength'=>250)); ?>
	</div>

	<div class="row">
		<?php $this->widget('ext.metro.fieldDateRow',array(
					'model'=>$model,
					'valor'=>'Fecha',
				)); 
			?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Monto',array('size'=>12,'maxlength'=>12)); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->