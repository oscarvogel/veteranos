<?php
$this->breadcrumbs=array(
	'Ingresoses',
);

$this->menu=array(
	array('label'=>'Crear Ingresos', 'url'=>array('create')),
	array('label'=>'Administrar Ingresos', 'url'=>array('admin')),
);
?>

<h1>Ingresoses</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
