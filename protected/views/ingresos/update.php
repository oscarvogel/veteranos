<?php
$this->breadcrumbs=array(
	'Ingresoses'=>array('index'),
	$model->idIngreso=>array('view','id'=>$model->idIngreso),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Ingresos', 'url'=>array('index')),
	array('label'=>'Crear Ingresos', 'url'=>array('create')),
	array('label'=>'Ver Ingresos', 'url'=>array('view', 'id'=>$model->idIngreso)),
	array('label'=>'Administrar Ingresos', 'url'=>array('admin')),
);
?>

<h1>Actualizar Ingresos <?php echo $model->idIngreso; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>