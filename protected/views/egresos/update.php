<?php
$this->breadcrumbs=array(
	'Egresoses'=>array('index'),
	$model->idEgreso=>array('view','id'=>$model->idEgreso),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Egresos', 'url'=>array('index')),
	array('label'=>'Crear Egresos', 'url'=>array('create')),
	array('label'=>'Ver Egresos', 'url'=>array('view', 'id'=>$model->idEgreso)),
	array('label'=>'Administrar Egresos', 'url'=>array('admin')),
);
?>

<h1>Actualizar Egresos <?php echo $model->idEgreso; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>