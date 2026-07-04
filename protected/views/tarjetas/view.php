<?php
$this->breadcrumbs=array(
	'Tarjetases'=>array('index'),
	$model->idTarjeta,
);

$this->menu=array(
	array('label'=>'Listar Tarjetas', 'url'=>array('index')),
	array('label'=>'Crear Tarjetas', 'url'=>array('create')),
	array('label'=>'Actualizar Tarjetas', 'url'=>array('update', 'id'=>$model->idTarjeta)),
	array('label'=>'Borrar Tarjetas', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idTarjeta),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Tarjetas', 'url'=>array('admin')),
);
?>

<h1>Ver Tarjetas #<?php echo $model->idTarjeta; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idTarjeta',
		'idFixture',
		'idJugador',
		'Amarilla',
		'Roja',
		'DesdeFecha',
		'HastaFecha',
		'Motivo',
	),
)); ?>
