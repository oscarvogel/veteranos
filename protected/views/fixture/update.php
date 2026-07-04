<?php
$this->breadcrumbs=array(
	'Fixtures'=>array('index'),
	$model->idFixture=>array('view','id'=>$model->idFixture),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Fixture', 'url'=>array('index')),
	array('label'=>'Crear Fixture', 'url'=>array('create')),
	array('label'=>'Ver Fixture', 'url'=>array('view', 'id'=>$model->idFixture)),
	array('label'=>'Administrar Fixture', 'url'=>array('admin')),
);
?>

<h1>Actualizar Fixture <?php echo $model->idFixture; ?></h1>

<?php echo $this->renderPartial('_form', 
		array('model'=>$model, 'goleador'=>$goleador, 'validatedMembers'=>$validatedMembers)); ?>