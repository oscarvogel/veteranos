<?php
$this->breadcrumbs=array(
	'Articulos'=>array('index'),
	$model->idArticulo=>array('view','id'=>$model->idArticulo),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Articulos', 'url'=>array('index')),
	array('label'=>'Crear Articulos', 'url'=>array('create')),
	array('label'=>'Ver Articulos', 'url'=>array('view', 'id'=>$model->idArticulo)),
	array('label'=>'Administrar Articulos', 'url'=>array('admin')),
);
?>

<h1>Actualizar Articulos <?php echo $model->idArticulo; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>