<?php
$this->breadcrumbs=array(
	'Noticiascels'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Noticiascel', 'url'=>array('index')),
	array('label'=>'Administrar Noticiascel', 'url'=>array('admin')),
);
?>

<h1>Crear Noticiascel</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>