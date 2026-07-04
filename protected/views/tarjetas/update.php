<?php
$this->breadcrumbs=array(
	'Tarjetases'=>array('index'),
	$model->idTarjeta=>array('view','id'=>$model->idTarjeta),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Tarjetas', 'url'=>array('index')),
	array('label'=>'Crear Tarjetas', 'url'=>array('create')),
	array('label'=>'Ver Tarjetas', 'url'=>array('view', 'id'=>$model->idTarjeta)),
	array('label'=>'Administrar Tarjetas', 'url'=>array('admin')),
);
?>

<h1>Actualizar Tarjetas <?php echo $model->idTarjeta; ?></h1>

<?php echo $this->renderPartial('_form', 
		array('model'=>$model,
			'idFixture'=>$idFixture,
			'idEquipo'=>$idEquipo,
		)); ?>