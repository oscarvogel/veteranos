<?php
$this->breadcrumbs=array(
	'Arbitroses'=>array('index'),
	$model->idArbitro,
);

$this->menu=array(
	array('label'=>'Listar Arbitros', 'url'=>array('index')),
	array('label'=>'Crear Arbitros', 'url'=>array('create')),
	array('label'=>'Actualizar Arbitros', 'url'=>array('update', 'id'=>$model->idArbitro)),
	array('label'=>'Borrar Arbitros', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idArbitro),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Arbitros', 'url'=>array('admin')),
);
?>

<h1>Ver Arbitros #<?php echo $model->idArbitro; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idArbitro',
		'Nombre',
		'Telefono',
		'Correo',
	),
)); ?>
