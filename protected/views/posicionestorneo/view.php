<?php
$this->breadcrumbs=array(
	'Posicionestorneos'=>array('index'),
	$model->idPosicionTorneo,
);

$this->menu=array(
	array('label'=>'Listar Posicionestorneo', 'url'=>array('index')),
	array('label'=>'Crear Posicionestorneo', 'url'=>array('create')),
	array('label'=>'Actualizar Posicionestorneo', 'url'=>array('update', 'id'=>$model->idPosicionTorneo)),
	array('label'=>'Borrar Posicionestorneo', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idPosicionTorneo),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Posicionestorneo', 'url'=>array('admin')),
);
?>

<h1>Ver Posicionestorneo #<?php echo $model->idPosicionTorneo; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idPosicionTorneo',
		'idTorneo',
		'idEquipo',
		'Posicion',
	),
)); ?>
