<?php
$this->breadcrumbs=array(
	'Jugadors'=>array('index'),'Administrar',
);

$this->menu=array(
	array('label'=>'Listar Jugador', 'url'=>array('index')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
	array('label'=>'Importar lista buena fe', 'url'=>array('importarListaBuenaFe')),
	array('label'=>'Liberar a Todos', 'url'=>array('liberar')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('jugador-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

Yii::app()->clientScript->registerScript('fecha-mask', "
$(document).ready(function() {
	$(document).on('shown.editable', function(e, editable) {
		var \$element = $(e.target);
		if(\$element.hasClass('fecha-input-mask')) {
			var \$input = \$element.find('input[type=\"text\"]');
			if(\$input.length === 0) {
				\$input = \$('input.editable-input');
			}
			if(\$input.length > 0) {
				\$input.attr('maxlength', '10');
				// Evento input para formatear mientras se escribe
				\$input.on('input', function() {
					var valor = \$(this).val().replace(/[^0-9]/g, '');
					var formateado = '';
					if(valor.length >= 1) formateado = valor.substring(0, 2);
					if(valor.length >= 3) formateado += '/' + valor.substring(2, 4);
					if(valor.length >= 5) formateado += '/' + valor.substring(4, 8);
					\$(this).val(formateado);
				});
				// Evento blur para agregar barras si falta
				\$input.on('blur', function() {
					var valor = \$(this).val().replace(/[^0-9]/g, '');
					if(valor.length === 8) {
						var formateado = valor.substring(0, 2) + '/' + valor.substring(2, 4) + '/' + valor.substring(4, 8);
						\$(this).val(formateado);
					}
				});
			}
		}
	});
});
");
?>

<h1>Administrar Jugadores</h1>

<p>Puede usar operadores de comparacion (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> o <b>=</b>) al comienzo de cada valor de busqueda.</p>

<?php echo CHtml::link('Busqueda Avanzada','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'jugador-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idEquipo',
              'filter' => Equipos::getListEquipo(),
              'value'=>'$data->Equipo->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => Yii::app()->createUrl('/jugador/Actualiza'),
                  'source'   => Yii::app()->createUrl('//equipos/GetEquipos'),
                   //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
	    array(
	           'class' => 'editable.EditableColumn',
	           'name' => 'Nombre',
	           'headerHtmlOptions' => array('style' => 'width: 110px'),
	           'editable' => array(
	                  'url'        => $this->createUrl('jugador/Actualiza'),
	                  'placement'  => 'right',
	              )               
	        ),

	    array(
	           'class' => 'editable.EditableColumn',
	           'name' => 'DNI',
	           'htmlOptions'=>array('type'=>'number'),
	           'editable' => array(
	                  'url'        => $this->createUrl('jugador/Actualiza'),
	                  'placement'  => 'right',
	              )               
	        ),
	    array(
	           'class' => 'editable.EditableColumn',
	           'name' => 'Observacion',
	           'headerHtmlOptions' => array('style' => 'width: 110px'),
	           'editable' => array(
	                  'url'        => $this->createUrl('jugador/Actualiza'),
	                  'placement'  => 'right',
	              )               
	        ),
	    array(
	           'class' => 'editable.EditableColumn',
	           'name' => 'fecha_nacimiento',
	           'headerHtmlOptions' => array('style' => 'width: 120px'),
	           'htmlOptions' => array('class' => 'fecha-input-mask'),
	           'value' => '$data->fecha_nacimiento ? date("d/m/Y", strtotime($data->fecha_nacimiento)) : ""',
	           'editable' => array(
	                  'type'       => 'text',
	                  'url'        => $this->createUrl('jugador/Actualiza'),
	                  'placement'  => 'right',
	                  'inputclass' => 'fecha-input',
	              )               
	        ),
        array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {update} {delete} {legajo}',
			'buttons'=>array(
				'legajo'=>array(
					'label'=>'Legajo',
					'url'=>'Yii::app()->createUrl("jugador/legajo", array("id"=>$data->idJugador))',
					'options'=>array('class'=>'btn btn-info btn-xs', 'title'=>'Legajo digital'),
				),
			),
		),
	),
)); ?>
