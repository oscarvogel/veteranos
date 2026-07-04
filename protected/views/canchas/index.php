<?php
$this->breadcrumbs=array(
	'Canchases',
);

$this->menu=array(
	array('label'=>'Crear Canchas', 'url'=>array('create')),
	array('label'=>'Administrar Canchas', 'url'=>array('admin')),
);
?>

<h1>Canchases</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
