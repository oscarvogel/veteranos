<?php
$this->breadcrumbs=array(
	'Goles'=>array('index'),
	$model->idGol=>array('view','id'=>$model->idGol),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Goles', 'url'=>array('index')),
	array('label'=>'Crear Goles', 'url'=>array('create')),
	array('label'=>'Ver Goles', 'url'=>array('view', 'id'=>$model->idGol)),
	array('label'=>'Administrar Goles', 'url'=>array('admin')),
);
?>

<h1>Actualizar Goles <?php echo $model->idGol; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>