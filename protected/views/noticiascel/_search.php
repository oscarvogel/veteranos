<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'idnoticiacel'); ?>
		<?php echo $form->textField($model,'idnoticiacel',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'noticia'); ?>
		<?php echo $form->textField($model,'noticia',array('size'=>60,'maxlength'=>200)); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Busqueda')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->