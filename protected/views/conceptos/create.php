<?php
$this->breadcrumbs=array(
	'Conceptoses'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Conceptos', 'url'=>array('index')),
	array('label'=>'Administrar Conceptos', 'url'=>array('admin')),
);
?>

<h1>Crear Conceptos</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>