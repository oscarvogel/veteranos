<?php
$this->breadcrumbs=array(
	'Articulos'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Articulos', 'url'=>array('index')),
	array('label'=>'Administrar Articulos', 'url'=>array('admin')),
);
?>

<h1>Crear Articulos</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>