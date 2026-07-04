<?php
$this->breadcrumbs=array(
	'Noticiascels'=>array('index'),
	$model->idnoticiacel=>array('view','id'=>$model->idnoticiacel),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Noticiascel', 'url'=>array('index')),
	array('label'=>'Crear Noticiascel', 'url'=>array('create')),
	array('label'=>'Ver Noticiascel', 'url'=>array('view', 'id'=>$model->idnoticiacel)),
	array('label'=>'Administrar Noticiascel', 'url'=>array('admin')),
);
?>

<h1>Actualizar Noticiascel <?php echo $model->idnoticiacel; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>