<?php
$this->breadcrumbs=array(
	'Torneos'=>array('index'),
	$model->idTorneo=>array('view','id'=>$model->idTorneo),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Torneo', 'url'=>array('index')),
	array('label'=>'Crear Torneo', 'url'=>array('create')),
	array('label'=>'Ver Torneo', 'url'=>array('view', 'id'=>$model->idTorneo)),
	array('label'=>'Administrar Torneo', 'url'=>array('admin')),
);
?>

<h1>Actualizar Torneo <?php echo $model->idTorneo; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>