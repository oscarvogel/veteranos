<?php
$this->breadcrumbs=array(
	'Recibos'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Registrar Pago', 'url'=>array('create')),
	array('label'=>'Arqueo de Caja', 'url'=>array('arqueoCaja')),
	array('label'=>'Resumen mensual', 'url'=>array('resumenMensual')),
	array('label'=>'Conceptos de cobro', 'url'=>array('/conceptos/admin')),
	array('label'=>'Arancel por Fecha', 'url'=>array('ArancelFecha')),
	array('label'=>'Ingresos por tipo', 'url'=>array('IngresoPorTipo')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('ingresos-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administrar recibos</h1>

<p>Si lo desea, puede entrar en un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
o <b>=</b>) al comienzo de cada uno de los valores de su búsqueda para especificar cómo la comparación se debe hacer.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'ingresos-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'NumeroRecibo',
		'Fecha',
		array(
			'name'=>'idEquipo',
			'value'=>'$data->Equipos->Nombre',
			'filter'=>Equipos::getListEquipo(),
		),
		array(
			'name'=>'idConcepto',
			'value'=>'$data->Conceptos->Nombre',
			'filter'=>Conceptos::getListConceptos(),
		),
		'Monto',
		'Estado',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {update} {pdf} {whatsapp} {anular}',
			'buttons'=>array(
				'pdf'=>array(
					'label'=>'PDF',
					'imageUrl'=>Yii::app()->baseUrl . '/media/iconos/recibo-pdf.png',
					'url'=>'Yii::app()->createUrl("ingresos/reciboPdf", array("id"=>$data->idIngreso))',
					'options'=>array('target'=>'_blank'),
				),
				'whatsapp'=>array(
					'label'=>'WhatsApp',
					'imageUrl'=>Yii::app()->baseUrl . '/media/iconos/recibo-whatsapp.png',
					'url'=>'$data->getWhatsappUrl()',
					'options'=>array('target'=>'_blank'),
				),
				'anular'=>array(
					'label'=>'Anular',
					'imageUrl'=>Yii::app()->baseUrl . '/media/iconos/recibo-anular.png',
					'url'=>'Yii::app()->createUrl("ingresos/anular", array("id"=>$data->idIngreso))',
					'visible'=>'$data->Estado !== "ANULADO"',
				),
			),
		),
	),
)); ?>
