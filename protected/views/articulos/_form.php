<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'articulos-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$model,
				'valor'=>'FechaPublicacion',
			));?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Activo'); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Titulo',array('size'=>60,'maxlength'=>200)); ?>
	</div>

	<div class="row">
		<?php $this->widget('ext.widgets.redactorjs.redactor',array(
            'model'=>$model,
            'attribute'=>'Introduccion',
            // Redactor options
            'editorOptions'=>array(
                'lang'=>'es',
            ),
        ));?>
	</div>

	<div class="row">
		<?php $this->widget('ext.widgets.redactorjs.redactor',array(
            'model'=>$model,
            'attribute'=>'Texto',
            // Redactor options
            'editorOptions'=>array(
                'lang'=>'es',
            ),
        ));?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->