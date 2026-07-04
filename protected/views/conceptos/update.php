<?php
$this->breadcrumbs=array(
	'Conceptoses'=>array('index'),
	$model->idConcepto=>array('view','id'=>$model->idConcepto),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Conceptos', 'url'=>array('index')),
	array('label'=>'Crear Conceptos', 'url'=>array('create')),
	array('label'=>'Ver Conceptos', 'url'=>array('view', 'id'=>$model->idConcepto)),
	array('label'=>'Administrar Conceptos', 'url'=>array('admin')),
);
?>

<h1>Actualizar Conceptos <?php echo $model->idConcepto; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>