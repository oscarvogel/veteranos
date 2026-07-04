<?php
$this->breadcrumbs=array(
	'Resoluciones'=>array('index'),
	$model->idResolucion=>array('view','id'=>$model->idResolucion),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Resoluciones', 'url'=>array('index')),
	array('label'=>'Crear Resoluciones', 'url'=>array('create')),
	array('label'=>'Ver Resoluciones', 'url'=>array('view', 'id'=>$model->idResolucion)),
	array('label'=>'Administrar Resoluciones', 'url'=>array('admin')),
);
?>

<h1>Actualizar Resoluciones <?php echo $model->idResolucion; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>