<?php
$this->breadcrumbs=array(
	'Egresoses'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Listar Egresos', 'url'=>array('index')),
	array('label'=>'Crear Egresos', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('egresos-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administrar Egresoses</h1>

<p>Si lo desea, puede entrar en un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
o <b>=</b>) al comienzo de cada uno de los valores de su búsqueda para especificar cómo la comparación se debe hacer.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'egresos-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'idConcepto',
			'value'=>'$data->Conceptos->Nombre',
			'filter'=>Conceptos::getListConceptos(),
		),
		'Detalle',
		'Fecha',
		'Monto',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
