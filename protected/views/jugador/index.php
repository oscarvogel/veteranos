<?php
$this->breadcrumbs=array(
	'Jugadors',
);

$this->menu=array(
	array('label'=>'Crear Jugador', 'url'=>array('create')),
	array('label'=>'Administrar Jugador', 'url'=>array('admin')),
);
?>

<h1>Jugadors</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
