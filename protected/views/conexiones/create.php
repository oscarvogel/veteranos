<?php
$this->breadcrumbs=array(
	'Conexiones'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Conexiones', 'url'=>array('index')),
	array('label'=>'Administrar Conexiones', 'url'=>array('admin')),
);
?>

<h1>Crear Conexiones</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>