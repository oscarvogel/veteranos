<?php
$this->breadcrumbs=array(
	'Equiposes'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Equipos', 'url'=>array('index')),
	array('label'=>'Administrar Equipos', 'url'=>array('admin')),
);
?>

<h1>Crear Equipos</h1>

<?php echo $this->renderPartial('_form', 
		array('model'=>$model,
		'jugador'=>$jugador,'validatedMembers'=>$validatedMembers)); ?>