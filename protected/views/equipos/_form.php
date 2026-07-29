<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'equipos-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Nombre',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Delegado',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'DelegadoSuplente',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Tecnico', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Tecnico', array('class'=>'form-control', 'maxlength'=>100)); ?>
			<?php echo CHtml::error($model, 'Tecnico', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'AyudanteTecnico', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'AyudanteTecnico', array('class'=>'form-control', 'maxlength'=>100)); ?>
			<?php echo CHtml::error($model, 'AyudanteTecnico', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Camiseta',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'CamisetaSuplente',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Correo',array('size'=>50,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'Telefono',array('size'=>50,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'Cancha',Canchas::model()->getListCancha()); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idCategoria',Categorias::model()->getListCategorias()); ?>
	</div>
    
	<div class="row">
		<?php echo $form->textFieldRow($model,'idUsuario'); ?>
	</div>    
	<?php
	$jugadorFormConfig = array(
      'elements'=>array(
        'Nombre'=>array(
            'type'=>'text',
            'maxlength'=>40,
        ),
        'Clase'=>array(
            'type'=>'number',
            'maxlength'=>4,
        ),
        'DNI'=>array(
            'type'=>'text',
            'maxlength'=>8,
        ),
    ));
	
	$this->widget('ext.multimodelform.MultiModelForm',array(
        'id' => 'id_member', //the unique widget id
        'formConfig' => $jugadorFormConfig, //the form configuration array
        'model' => $jugador, //instance of the form model
 		'addItemText' => 'Agregar Jugador',
 		'addItemAsButton' => true,
 		'removeText' => 'Borrar', 
 		'removeConfirm' => 'Borra el Jugador?', 
 		'tableView' => true,
 		'tableHtmlOptions' => array('class'=>'table'),
 		'bootstrapLayout' => true,
 		'sortAttribute' => 'Nombre',
        //if submitted not empty from the controller,
        //the form will be rendered with validation errors
        'validatedItems' => $validatedMembers,
 
        //array of member instances loaded from db
        'data' => $jugador->findAll(array('order'=>'Nombre','condition'=>'idEquipo=:idEquipo', 'params'=>array(':idEquipo'=>$model->idEquipo))),
    ));
	?>
	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
