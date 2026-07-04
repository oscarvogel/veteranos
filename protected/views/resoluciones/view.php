<?php
$this->breadcrumbs=array(
	'Resoluciones'=>array('index'),
	$model->idResolucion,
);

$this->menu=array(
	array('label'=>'Listar Resoluciones', 'url'=>array('index')),
	array('label'=>'Crear Resoluciones', 'url'=>array('create')),
	array('label'=>'Actualizar Resoluciones', 'url'=>array('update', 'id'=>$model->idResolucion)),
	array('label'=>'Borrar Resoluciones', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idResolucion),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Resoluciones', 'url'=>array('admin')),
);
?>

<h1>Ver Resoluciones #<?php echo $model->idResolucion; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idResolucion',
		'Fecha',
		'URL',
	),
)); ?>
