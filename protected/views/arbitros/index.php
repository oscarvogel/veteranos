<?php
$this->breadcrumbs=array(
	'Arbitroses',
);

$this->menu=array(
	array('label'=>'Crear Arbitros', 'url'=>array('create')),
	array('label'=>'Administrar Arbitros', 'url'=>array('admin')),
);
?>

<h1>Arbitroses</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
