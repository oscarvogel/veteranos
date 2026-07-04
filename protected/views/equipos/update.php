<?php
$this->breadcrumbs=array(
	'Equiposes'=>array('index'),
	$model->idEquipo=>array('view','id'=>$model->idEquipo),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Equipos', 'url'=>array('index')),
	array('label'=>'Crear Equipos', 'url'=>array('create')),
	array('label'=>'Ver Equipos', 'url'=>array('view', 'id'=>$model->idEquipo)),
	array('label'=>'Administrar Equipos', 'url'=>array('admin')),
);
?>

<h1>Actualizar Equipos <?php echo $model->idEquipo; ?></h1>

<?php echo $this->renderPartial('_form', 
		array('model'=>$model,
		'jugador'=>$jugador,'validatedMembers'=>$validatedMembers)); ?>