<?php
$this->breadcrumbs=array(
	'Posicionestorneos',
);

$this->menu=array(
	array('label'=>'Crear Posicionestorneo', 'url'=>array('create')),
	array('label'=>'Administrar Posicionestorneo', 'url'=>array('admin')),
);
?>

<h1>Posicionestorneos</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
