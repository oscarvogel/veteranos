<?php
$this->breadcrumbs=array(
	'Conexiones'=>array('index'),
	$model->idConexion=>array('view','id'=>$model->idConexion),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Conexiones', 'url'=>array('index')),
	array('label'=>'Crear Conexiones', 'url'=>array('create')),
	array('label'=>'Ver Conexiones', 'url'=>array('view', 'id'=>$model->idConexion)),
	array('label'=>'Administrar Conexiones', 'url'=>array('admin')),
);
?>

<h1>Actualizar Conexiones <?php echo $model->idConexion; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>