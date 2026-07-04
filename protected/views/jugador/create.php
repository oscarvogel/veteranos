<?php
$this->breadcrumbs=array(
	'Jugadors'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Jugador', 'url'=>array('index')),
	array('label'=>'Administrar Jugador', 'url'=>array('admin')),
);
?>

<h1>Crear Jugador</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>