<?php
$this->breadcrumbs=array(
	'Canchases'=>array('index'),
	$model->idCancha,
);

$this->menu=array(
	array('label'=>'Listar Canchas', 'url'=>array('index')),
	array('label'=>'Crear Canchas', 'url'=>array('create')),
	array('label'=>'Actualizar Canchas', 'url'=>array('update', 'id'=>$model->idCancha)),
	array('label'=>'Borrar Canchas', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idCancha),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Canchas', 'url'=>array('admin')),
);
?>

<h1>Ver Canchas #<?php echo $model->idCancha; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idCancha',
		'Nombre',
		'Titular',
		'Telefono',
	),
)); ?>
