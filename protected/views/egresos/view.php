<?php
$this->breadcrumbs=array(
	'Egresoses'=>array('index'),
	$model->idEgreso,
);

$this->menu=array(
	array('label'=>'Listar Egresos', 'url'=>array('index')),
	array('label'=>'Crear Egresos', 'url'=>array('create')),
	array('label'=>'Actualizar Egresos', 'url'=>array('update', 'id'=>$model->idEgreso)),
	array('label'=>'Borrar Egresos', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idEgreso),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Egresos', 'url'=>array('admin')),
);
?>

<h1>Ver Egresos #<?php echo $model->idEgreso; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idEgreso',
		'idConcepto',
		'Detalle',
		'Fecha',
		'Monto',
	),
)); ?>
