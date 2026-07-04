<?php
$this->breadcrumbs=array(
	'Jugadors'=>array('index'),
	$model->idJugador,
);

$this->menu=array(
	array('label'=>'Listar Jugador', 'url'=>array('index')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
	array('label'=>'Actualizar Jugador', 'url'=>array('update', 'id'=>$model->idJugador)),
	array('label'=>'Borrar Jugador', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idJugador),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Jugador', 'url'=>array('admin')),
);
?>

<h1>Ver Jugador #<?php echo $model->idJugador; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idJugador',
		'Nombre',
		'Clase',
		'DNI',
		'idEquipo',
	),
)); ?>
