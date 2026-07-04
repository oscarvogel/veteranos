<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'fixture-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Campos con <span class="required">* </span>son requeridos.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo()); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'NFecha',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$model,
				'valor'=>'Fecha',
			)); 
		?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'Local',Equipos::getListEquipo()); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'Visitante',Equipos::getListEquipo()); ?>
	</div>

	<div class="row">
		<?php echo $form->dropDownListRow($model,'idCancha',Canchas::getListCancha()); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'GolLocal',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->textFieldRow($model,'GolVisitante',array('size'=>2,'maxlength'=>2)); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBoxRow($model,'PostTemporada'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBoxRow($model,'interzonal'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBoxRow($model,'SumaPuntos', array('checked'=>'checked')); ?>
	</div>

	<div class="row">
		<?php 
		$this->widget('ext.coco.CocoWidget',array(
            		'id'=>'cocowidget1',
            		'onCompleted'=>'function(id,filename,jsoninfo){
            			$("#Fixture_Archivo").val("/media/pdf/"+filename);
            		}',
            		'onCancelled'=>'function(id,filename){ alert("cancelled"); }',
            		'onMessage'=>'function(m){ alert(m); }',
            		'allowedExtensions'=>array('pdf'),
            		'sizeLimit'=>4000000,
            		'uploadDir' => 'media/pdf',
            		'maxUploads'=>1,
            		'buttonText'=>'Encontrar & Subir',
					'dropFilesText'=>'Suelte los archivos aqui !',
					'maxUploadsReachMessage'=>'No se permiten mas archivos',
        ));

		echo $form->textFieldRow($model,'Archivo',array('size'=>60,'maxlength'=>200)); ?>
	</div>

	<?php
	$golesFormConfig = array(
      'elements'=>array(
        'idJugador'=>array(
            'type'=>'dropdownlist',
            'items'=>Jugador::getListJugador($model->Local, $model->Visitante),
        ),
        'Cantidad'=>array(
            'type'=>'number',
            'maxlength'=>4,
        ),
    ));
	
	$this->widget('ext.multimodelform.MultiModelForm',array(
        'id' => 'id_goles', //the unique widget id
        'formConfig' => $golesFormConfig, //the form configuration array
        'model' => $goleador, //instance of the form model
 		'addItemText' => 'Agregar Gol',
 		'addItemAsButton' => true,
 		'removeText' => 'Borrar', 
 		'removeConfirm' => 'Borra el Gol?', 
 		'tableView' => true,
 		'tableHtmlOptions' => array('class'=>'table'),
 		'bootstrapLayout' => true,
 		'sortAttribute' => 'Nombre',
        //if submitted not empty from the controller,
        //the form will be rendered with validation errors
        'validatedItems' => $validatedMembers,
 
        //array of member instances loaded from db
        'data' => $goleador->findAll('idFixture=:idFixture', array(':idFixture'=>$model->idFixture)),
    ));
	?>

	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => $model->isNewRecord ? 'Crear' : 'Guardar')); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Guardar Puntos','htmlOptions'=>array('name'=>'btnGuardaPuntos'))); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

