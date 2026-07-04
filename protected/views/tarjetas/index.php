<?php
$this->breadcrumbs=array(
	'Tarjetases',
);

$this->menu=array(
	array('label'=>'Crear Tarjetas', 'url'=>array('create')),
	array('label'=>'Administrar Tarjetas', 'url'=>array('admin')),
);
?>

<h1>Tarjetases</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
