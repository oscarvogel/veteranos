<?php
$this->breadcrumbs=array(
	'Torneos'=>array('index'),
	$model->idTorneo,
);

$this->menu=array(
	array('label'=>'Listar Torneo', 'url'=>array('index')),
	array('label'=>'Crear Torneo', 'url'=>array('create')),
	array('label'=>'Actualizar Torneo', 'url'=>array('update', 'id'=>$model->idTorneo)),
	array('label'=>'Borrar Torneo', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idTorneo),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Torneo', 'url'=>array('admin')),
);
?>

<h1>Ver Torneo #<?php echo $model->idTorneo; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idTorneo',
		'Nombre',
		'Inicio',
		'Estado',
	),
)); ?>
