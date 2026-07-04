<?php
$this->breadcrumbs=array(
	'Egresoses'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Egresos', 'url'=>array('index')),
	array('label'=>'Administrar Egresos', 'url'=>array('admin')),
);
?>

<h1>Crear Egresos</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>