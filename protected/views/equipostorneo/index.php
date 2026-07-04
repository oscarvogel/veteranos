<?php
$this->breadcrumbs=array(
	'Equipostorneos',
);

$this->menu=array(
	array('label'=>'Crear Equipostorneo', 'url'=>array('create')),
	array('label'=>'Administrar Equipostorneo', 'url'=>array('admin')),
);
?>

<h1>Equipostorneos</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
