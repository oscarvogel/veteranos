<?php
$this->breadcrumbs=array(
	'Articuloses'=>array('index'),
	$model->idArticulo,
);

$this->menu=array(
	array('label'=>'Listar Articulos', 'url'=>array('index')),
	array('label'=>'Crear Articulos', 'url'=>array('create')),
	array('label'=>'Actualizar Articulos', 'url'=>array('update', 'id'=>$model->idArticulo)),
	array('label'=>'Borrar Articulos', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idArticulo),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Articulos', 'url'=>array('admin')),
);
?>

<h1>Ver Articulos #<?php echo $model->idArticulo; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idArticulo',
		'FechaPublicacion',
		'Activo',
		'Titulo',
	),
)); ?>
