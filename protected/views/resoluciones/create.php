<?php
$this->breadcrumbs=array(
	'Resoluciones'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Resoluciones', 'url'=>array('index')),
	array('label'=>'Administrar Resoluciones', 'url'=>array('admin')),
);
?>

<h1>Crear Resoluciones</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>