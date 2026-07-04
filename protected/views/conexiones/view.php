<?php
$this->breadcrumbs=array(
	'Conexiones'=>array('index'),
	$model->idConexion,
);

$this->menu=array(
	array('label'=>'Listar Conexiones', 'url'=>array('index')),
	array('label'=>'Crear Conexiones', 'url'=>array('create')),
	array('label'=>'Actualizar Conexiones', 'url'=>array('update', 'id'=>$model->idConexion)),
	array('label'=>'Borrar Conexiones', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idConexion),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Conexiones', 'url'=>array('admin')),
);
?>

<h1>Ver Conexiones #<?php echo $model->idConexion; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idConexion',
		'latitud',
		'longitud',
		'altitud',
		'horario',
	),
)); ?>
