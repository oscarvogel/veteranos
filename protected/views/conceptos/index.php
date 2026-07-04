<?php
$this->breadcrumbs=array(
	'Conceptoses',
);

$this->menu=array(
	array('label'=>'Crear Conceptos', 'url'=>array('create')),
	array('label'=>'Administrar Conceptos', 'url'=>array('admin')),
);
?>

<h1>Conceptoses</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
