<?php
$this->breadcrumbs=array(
	'Conexiones',
);

$this->menu=array(
	array('label'=>'Crear Conexiones', 'url'=>array('create')),
	array('label'=>'Administrar Conexiones', 'url'=>array('admin')),
);
?>

<h1>Conexiones</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
