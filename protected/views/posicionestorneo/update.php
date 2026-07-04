<?php
$this->breadcrumbs=array(
	'Posicionestorneos'=>array('index'),
	$model->idPosicionTorneo=>array('view','id'=>$model->idPosicionTorneo),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Posicionestorneo', 'url'=>array('index')),
	array('label'=>'Crear Posicionestorneo', 'url'=>array('create')),
	array('label'=>'Ver Posicionestorneo', 'url'=>array('view', 'id'=>$model->idPosicionTorneo)),
	array('label'=>'Administrar Posicionestorneo', 'url'=>array('admin')),
);
?>

<h1>Actualizar Posicionestorneo <?php echo $model->idPosicionTorneo; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>