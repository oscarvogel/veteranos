<?php
$this->breadcrumbs=array(
	'Equipostorneos'=>array('index'),
	$model->idEquTorneo,
);

$this->menu=array(
	array('label'=>'Listar Equipostorneo', 'url'=>array('index')),
	array('label'=>'Crear Equipostorneo', 'url'=>array('create')),
	array('label'=>'Actualizar Equipostorneo', 'url'=>array('update', 'id'=>$model->idEquTorneo)),
	array('label'=>'Borrar Equipostorneo', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idEquTorneo),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Equipostorneo', 'url'=>array('admin')),
);
?>

<h1>Ver Equipostorneo #<?php echo $model->idEquTorneo; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idEquTorneo',
		'idEquipo',
		'idTorneo',
		'lista',
	),
)); ?>
