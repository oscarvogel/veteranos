<?php
$this->breadcrumbs=array(
	'Equipostorneos'=>array('index'),
	$model->idEquTorneo=>array('view','id'=>$model->idEquTorneo),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Equipostorneo', 'url'=>array('index')),
	array('label'=>'Crear Equipostorneo', 'url'=>array('create')),
	array('label'=>'Ver Equipostorneo', 'url'=>array('view', 'id'=>$model->idEquTorneo)),
	array('label'=>'Administrar Equipostorneo', 'url'=>array('admin')),
);
?>

<h1>Actualizar Equipostorneo <?php echo $model->idEquTorneo; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>