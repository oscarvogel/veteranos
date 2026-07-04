<?php
$this->breadcrumbs=array(
	'Resoluciones',
);?>

<h1>Resoluciones</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
