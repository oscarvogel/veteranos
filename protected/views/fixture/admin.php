<?php
$this->breadcrumbs=array(
	'Fixtures'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Listar Fixture', 'url'=>array('index')),
	array('label'=>'Crear Fixture', 'url'=>array('create')),
	array('label'=>'Cambia Fecha', 'url'=>array('CambiaFecha')),
	array('label'=>'Copia Fixture', 'url'=>array('CopiaFixture')),
	array('label'=>'Adelanta Fechas', 'url'=>array('AdelantaFecha')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('fixture-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administrar Fixtures</h1>

<p>Si lo desea, puede entrar en un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
o <b>=</b>) al comienzo de cada uno de los valores de su búsqueda para especificar cómo la comparación se debe hacer.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'fixture-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'idTorneo',
			'value'=>'$data->Torneo->Nombre',
			'filter'=>Torneo::getListTorneo(),
		),		
		array(
			'name'=>'Local',
			'value'=>'$data->local->Nombre',
			'filter'=>Equipos::getListEquipo(),
		),
		array(
			'name'=>'Visitante',
			'value'=>'$data->visitante->Nombre',
			'filter'=>Equipos::getListEquipo(),
		),
		'NFecha',
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idCancha',
              'value'=>'$data->Cancha->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/update'),
                  'source'   => Yii::app()->createUrl('//canchas/GetCanchas'),
                  'options'  => array(    //custom display 
                     'display' => 'js: function(value, sourceData) {
                          var selected = $.grep(sourceData, function(o){ return value == o.value; }),
                              colors = {1: "green", 2: "blue", 3: "red", 4: "gray"};
                          $(this).text(selected[0].text).css("color", colors[value]);    
                      }'
                  ),
                 //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
