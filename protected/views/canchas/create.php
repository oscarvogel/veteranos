<?php
$this->breadcrumbs=array(
	'Canchases'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Canchas', 'url'=>array('index')),
	array('label'=>'Administrar Canchas', 'url'=>array('admin')),
);
?>

<h1>Crear Canchas</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>