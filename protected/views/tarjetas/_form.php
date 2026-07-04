<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'tarjetas-form',
	'type'=>'horizontal',
	'focus'=>array($model,'idJugador'),
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>
	<?php if(!$model->isNewRecord) 
		$this->widget('bootstrap.widgets.TbLabel', array(
		    'type'=>'important', // 'success', 'warning', 'important', 'info' or 'inverse'
		    'label'=>'Fue expulsado en la ' . $model->Fixture->NFecha . ' fecha',
		)); ?>
	<div class="row">
		<?php echo $form->hiddenField($model,'idFixture',array('size'=>10,'maxlength'=>10, 'value'=>$idFixture)); ?>
	</div>

	<div class="row">
		<?php if(isset($torneo)){
			echo $form->dropDownListRow($model,'idJugador',Jugador::getListJugador($idEquipo, $idEquipo, $torneo));
		}else{
			echo $form->dropDownListRow($model,'idJugador',Jugador::getListJugador($idEquipo, $idEquipo));
		} 
		?>
	</div>

	<div class="row">
		<?php echo $form->checkBoxRow($model,'Amarilla'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBoxRow($model,'Roja'); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'DesdeFecha',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'HastaFecha'); ?>
	</div>

	<div class="row">
		<?php echo $form->textAreaRow($model,'Motivo',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->