<?php
$this->breadcrumbs=array(
	'Goles'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Goles', 'url'=>array('index')),
	array('label'=>'Administrar Goles', 'url'=>array('admin')),
);
?>

<h1>Crear Goles</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>