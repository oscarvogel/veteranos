<?php
$this->breadcrumbs=array(
	'Arbitroses'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Arbitros', 'url'=>array('index')),
	array('label'=>'Administrar Arbitros', 'url'=>array('admin')),
);
?>

<h1>Crear Arbitros</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>