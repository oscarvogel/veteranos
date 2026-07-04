<?php
$this->breadcrumbs=array(
	'Canchases'=>array('index'),
	$model->idCancha=>array('view','id'=>$model->idCancha),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Canchas', 'url'=>array('index')),
	array('label'=>'Crear Canchas', 'url'=>array('create')),
	array('label'=>'Ver Canchas', 'url'=>array('view', 'id'=>$model->idCancha)),
	array('label'=>'Administrar Canchas', 'url'=>array('admin')),
);
?>

<h1>Actualizar Canchas <?php echo $model->idCancha; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>