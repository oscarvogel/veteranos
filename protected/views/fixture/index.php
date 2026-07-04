<?php
$this->breadcrumbs=array(
	'Fixtures',
);

$this->menu=array(
	array('label'=>'Crear Fixture', 'url'=>array('create')),
	array('label'=>'Administrar Fixture', 'url'=>array('admin')),
);
?>

<h1>Fixtures</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
