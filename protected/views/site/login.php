<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('app','Login');
$this->breadcrumbs=array(
	Yii::t('app','Login'),
);
?>

<h1><?php echo Yii::t('app','Login');?></h1>

<p><?php echo Yii::t('app','Please fill out the following form with your login credentials');?>:</p>

<div class="form">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'login-form',
	'htmlOptions'=>array('class'=>'well'),
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note"><?php echo Yii::t('app','Fields with ');?><span class="required">* </span> <?php echo Yii::t('app','are required');?>.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Yii::t('app','Login'))); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', 
			array('label'=>Yii::t('app','Register'),'type'=>'inverse', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
				'url'=>Yii::app()->createUrl('usuarios/create'),
		)); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
