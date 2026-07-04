<?php
$this->breadcrumbs=array(
	'Torneos'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Torneo', 'url'=>array('index')),
	array('label'=>'Administrar Torneo', 'url'=>array('admin')),
);
?>

<h1>Crear Torneo</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>