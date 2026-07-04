<?php
$this->breadcrumbs=array(
	'Fixtures'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Fixture', 'url'=>array('index')),
	array('label'=>'Administrar Fixture', 'url'=>array('admin')),
);
?>

<h1>Crear Fixture</h1>

<?php echo $this->renderPartial('_form', 
		array('model'=>$model, 'goleador'=>$goleador, 'validatedMembers'=>$validatedMembers)); ?>