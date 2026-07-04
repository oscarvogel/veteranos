<?php
$this->breadcrumbs=array(
	'Goles',
);

$this->menu=array(
	array('label'=>'Crear Goles', 'url'=>array('create')),
	array('label'=>'Administrar Goles', 'url'=>array('admin')),
);
?>

<h1>Goles</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
