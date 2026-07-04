<div class="form caja-form">

<?php
Yii::app()->clientScript->registerScript('ingresos-fecha-datepicker', "
$('#Ingresos_Fecha').datepicker({
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	changeYear: true
});
", CClientScript::POS_READY);
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'ingresos-form',
	'type'=>'vertical',
	'enableAjaxValidation'=>true,
	'htmlOptions'=>array('class'=>'caja-form-inner'),
)); ?>

	<div class="caja-payment-card">
		<div class="caja-payment-card__header">
			<h2>Datos del pago</h2>
			<p>Los campos marcados con <span class="required">*</span> son obligatorios.</p>
		</div>

		<div class="caja-payment-card__body">
			<?php echo $form->errorSummary($model); ?>

			<div class="caja-form-grid">
				<div class="form-row">
					<?php echo $form->labelEx($model,'idEquipo'); ?>
					<?php echo $form->dropDownList($model,'idEquipo',Equipos::getListEquipo(),array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'idEquipo'); ?>
				</div>

				<div class="form-row">
					<?php echo $form->labelEx($model,'Fecha'); ?>
					<?php echo $form->textField($model,'Fecha',array('class'=>'form-control fecha-datepicker','autocomplete'=>'off')); ?>
					<?php echo $form->error($model,'Fecha'); ?>
				</div>

				<div class="form-row">
					<?php echo $form->labelEx($model,'idConcepto'); ?>
					<?php echo $form->dropDownList($model,'idConcepto',Conceptos::getListConceptos(),array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'idConcepto'); ?>
				</div>

				<div class="form-row">
					<?php echo $form->labelEx($model,'Monto'); ?>
					<?php echo $form->textField($model,'Monto',array('class'=>'form-control monto-input','size'=>12,'maxlength'=>12,'inputmode'=>'decimal')); ?>
					<?php echo $form->error($model,'Monto'); ?>
				</div>

				<div class="form-row caja-field-full">
					<?php echo $form->labelEx($model,'Detalle'); ?>
					<?php echo $form->textArea($model,'Detalle',array('class'=>'form-control','maxlength'=>200,'rows'=>3)); ?>
					<?php echo $form->error($model,'Detalle'); ?>
				</div>
			</div>

			<div class="caja-actions">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Crear y agregar otro' : 'Guardar y agregar otro', array(
					'name'=>'btnAgregar',
					'class'=>'btn btn-default',
				)); ?>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Crear recibo' : 'Guardar recibo', array(
					'class'=>'btn btn-primary',
				)); ?>
			</div>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
