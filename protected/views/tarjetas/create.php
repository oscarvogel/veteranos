<?php
$this->breadcrumbs=array(
	'Tarjetases'=>array('index'),'Crear',
);

$this->menu=array(
	array('label'=>'Listar Tarjetas', 'url'=>array('index')),
	array('label'=>'Administrar Tarjetas', 'url'=>array('admin')),
);
?>

<h1>Crear Tarjetas</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
)); ?>