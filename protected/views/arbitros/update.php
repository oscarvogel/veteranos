<?php
$this->breadcrumbs=array(
	'Arbitroses'=>array('index'),
	$model->idArbitro=>array('view','id'=>$model->idArbitro),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Arbitros', 'url'=>array('index')),
	array('label'=>'Crear Arbitros', 'url'=>array('create')),
	array('label'=>'Ver Arbitros', 'url'=>array('view', 'id'=>$model->idArbitro)),
	array('label'=>'Administrar Arbitros', 'url'=>array('admin')),
);
?>

<h1>Actualizar Arbitros <?php echo $model->idArbitro; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>