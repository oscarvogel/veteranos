<?php
$this->breadcrumbs=array(
	'Tarjetases'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Listar Tarjetas', 'url'=>array('index')),
	array('label'=>'Crear Tarjetas', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tarjetas-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administrar Tarjetases</h1>

<p>Si lo desea, puede entrar en un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
o <b>=</b>) al comienzo de cada uno de los valores de su búsqueda para especificar cómo la comparación se debe hacer.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'tarjetas-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'idFixture',
			'value'=>'$data->Fixture->NFecha',
		),
		array(
			'name'=>'idJugador',
			'value'=>'$data->Jugador->Nombre',
			'filter'=>Jugador::getListJugador(null,null),
		),
		/*'Amarilla',
		'Roja',
		'DesdeFecha',
		'HastaFecha',
		'Motivo',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}{update}{delete}',
			'buttons'=>array(       
				 'update' => array(
                       'url'=>'Yii::app()->createUrl("tarjetas/update", array("id"=>$data->idTarjeta,"idFixture"=>$data->idFixture,"idEquipo"=>$data->Jugador->Equipo->idEquipo))',
                  ),)
		),
	),
)); ?>
