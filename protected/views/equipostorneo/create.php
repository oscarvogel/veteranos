<?php
$this->breadcrumbs=array(
	'Equipostorneos'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Equipostorneo', 'url'=>array('index')),
	array('label'=>'Administrar Equipostorneo', 'url'=>array('admin')),
);
?>

<h1>Crear Equipostorneo</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>