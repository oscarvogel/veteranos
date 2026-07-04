<?php
$this->breadcrumbs=array(
	'Noticiascels',
);

$this->menu=array(
	array('label'=>'Crear Noticiascel', 'url'=>array('create')),
	array('label'=>'Administrar Noticiascel', 'url'=>array('admin')),
);
?>

<h1>Noticiascels</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
