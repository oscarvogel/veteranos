<?php
$this->breadcrumbs=array(
	'Goles'=>array('index'),
	$model->idGol,
);

$this->menu=array(
	array('label'=>'Listar Goles', 'url'=>array('index')),
	array('label'=>'Crear Goles', 'url'=>array('create')),
	array('label'=>'Actualizar Goles', 'url'=>array('update', 'id'=>$model->idGol)),
	array('label'=>'Borrar Goles', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idGol),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Goles', 'url'=>array('admin')),
);
?>

<h1>Ver Goles #<?php echo $model->idGol; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idGol',
		'idJugador',
		'idFixture',
		'Cantidad',
	),
)); ?>
