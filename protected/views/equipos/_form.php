<div class="form">

<?php echo CHtml::beginForm('', 'post', array(
	'id'=>'equipos-form',
	'class'=>'form-horizontal',
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo CHtml::errorSummary($model); ?>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Nombre', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Nombre', array('class'=>'form-control', 'maxlength'=>50)); ?>
			<?php echo CHtml::error($model, 'Nombre', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Delegado', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Delegado', array('class'=>'form-control', 'maxlength'=>50)); ?>
			<?php echo CHtml::error($model, 'Delegado', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'DelegadoSuplente', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'DelegadoSuplente', array('class'=>'form-control', 'maxlength'=>50)); ?>
			<?php echo CHtml::error($model, 'DelegadoSuplente', array('class'=>'help-block')); ?>
		</div>
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

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Camiseta', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Camiseta', array('class'=>'form-control', 'maxlength'=>50)); ?>
			<?php echo CHtml::error($model, 'Camiseta', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'CamisetaSuplente', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'CamisetaSuplente', array('class'=>'form-control', 'maxlength'=>50)); ?>
			<?php echo CHtml::error($model, 'CamisetaSuplente', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Correo', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Correo', array('class'=>'form-control', 'maxlength'=>255)); ?>
			<?php echo CHtml::error($model, 'Correo', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Telefono', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'Telefono', array('class'=>'form-control', 'maxlength'=>100)); ?>
			<?php echo CHtml::error($model, 'Telefono', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'Cancha', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeDropDownList($model, 'Cancha', Canchas::model()->getListCancha(), array('class'=>'form-control')); ?>
			<?php echo CHtml::error($model, 'Cancha', array('class'=>'help-block')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'idCategoria', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeDropDownList($model, 'idCategoria', Categorias::model()->getListCategorias(), array('class'=>'form-control')); ?>
			<?php echo CHtml::error($model, 'idCategoria', array('class'=>'help-block')); ?>
		</div>
	</div>
    
	<div class="form-group">
		<?php echo CHtml::activeLabelEx($model, 'idUsuario', array('class'=>'control-label col-sm-3')); ?>
		<div class="col-sm-9">
			<?php echo CHtml::activeTextField($model, 'idUsuario', array('class'=>'form-control')); ?>
			<?php echo CHtml::error($model, 'idUsuario', array('class'=>'help-block')); ?>
		</div>
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
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar', array('class'=>'btn btn-primary')); ?>
		</div>
	</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->
