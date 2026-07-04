<?php
$this->breadcrumbs=array(
	'Articuloses',
);

$this->menu=array(
	array('label'=>'Crear Articulos', 'url'=>array('create')),
	array('label'=>'Administrar Articulos', 'url'=>array('admin')),
);
?>

<h1>Articulos</h1>

<?php $this->widget('bootstrap.widgets.TbListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
