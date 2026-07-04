<?php
$this->breadcrumbs=array(
	'Equiposes',
);

$this->menu=array(
	array('label'=>'Crear Equipos', 'url'=>array('create')),
	array('label'=>'Administrar Equipos', 'url'=>array('admin')),
);
?>

<h1>Equiposes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
