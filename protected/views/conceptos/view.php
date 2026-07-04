<?php
$this->breadcrumbs=array(
	'Conceptoses'=>array('index'),
	$model->idConcepto,
);

$this->menu=array(
	array('label'=>'Listar Conceptos', 'url'=>array('index')),
	array('label'=>'Crear Conceptos', 'url'=>array('create')),
	array('label'=>'Actualizar Conceptos', 'url'=>array('update', 'id'=>$model->idConcepto)),
	array('label'=>'Borrar Conceptos', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idConcepto),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Conceptos', 'url'=>array('admin')),
);
?>

<h1>Ver Conceptos #<?php echo $model->idConcepto; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idConcepto',
		'Nombre',
		'Monto',
	),
)); ?>
