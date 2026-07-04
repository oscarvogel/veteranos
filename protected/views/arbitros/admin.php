<?php
$this->breadcrumbs=array(
	'Arbitroses'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Listar Arbitros', 'url'=>array('index')),
	array('label'=>'Crear Arbitros', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('arbitros-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administrar Arbitroses</h1>

<p>Si lo desea, puede entrar en un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
o <b>=</b>) al comienzo de cada uno de los valores de su búsqueda para especificar cómo la comparación se debe hacer.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'arbitros-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'idArbitro',
		'Nombre',
		'Telefono',
		'Correo',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
