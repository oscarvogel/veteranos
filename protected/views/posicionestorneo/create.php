<?php
$this->breadcrumbs=array(
	'Posicionestorneos'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Posicionestorneo', 'url'=>array('index')),
	array('label'=>'Administrar Posicionestorneo', 'url'=>array('admin')),
);
?>

<h1>Crear Posicionestorneo</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>