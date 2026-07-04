<?php
$this->breadcrumbs=array(
	'Noticiascels'=>array('index'),
	$model->idnoticiacel,
);

$this->menu=array(
	array('label'=>'Listar Noticiascel', 'url'=>array('index')),
	array('label'=>'Crear Noticiascel', 'url'=>array('create')),
	array('label'=>'Actualizar Noticiascel', 'url'=>array('update', 'id'=>$model->idnoticiacel)),
	array('label'=>'Borrar Noticiascel', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idnoticiacel),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Noticiascel', 'url'=>array('admin')),
);
?>

<h1>Ver Noticiascel #<?php echo $model->idnoticiacel; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idnoticiacel',
		'noticia',
	),
)); ?>
