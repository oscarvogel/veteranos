<?php
/* @var $this PosicionesController */

$this->breadcrumbs=array(
	'Posiciones'=>array('index'),
	'Resumen de Fecha',
);
?>

<h3>Resumen de Fecha</h3>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'resumenFechaForm',
	'type'=>'inline',
	'htmlOptions'=>array('class'=>'well'),
));
?>
<div class="form-group">
	<?php echo $form->dropDownListRow($model, 'idTorneo', Torneo::getListTorneo('I'), array('class'=>'form-control')); ?>
</div>
<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow', array(
		'model'=>$model,
		'valor'=>'Fecha',
	)); ?>
</div>
<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Ver resumen', 'htmlOptions'=>array('class'=>'button primary', 'name'=>'btnResumen'))); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'PDF', 'htmlOptions'=>array('class'=>'button success', 'name'=>'btnPdf', 'target'=>'_blank'))); ?>
</div>
<?php $this->endWidget(); ?>

<?php echo $this->renderPartial('resumenFechaPdf', array(
	'idTorneo'=>$idTorneo,
	'fecha'=>$fecha,
	'torneo'=>$torneo,
	'resultados'=>$resultados,
	'posiciones'=>$posiciones,
	'goleadores'=>$goleadores,
	'tarjetasAmarillas'=>$tarjetasAmarillas,
)); ?>
