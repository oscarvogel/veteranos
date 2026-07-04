<?php
$this->breadcrumbs=array(
	'Jugadores'=>array('index'),
	'Documentacion',
);

$this->menu=array(
	array('label'=>'Administrar Jugadores', 'url'=>array('admin')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
);
?>

<h1>Documentacion de Jugadores</h1>

<p>Los estados se actualizan automaticamente desde los archivos cargados en el legajo digital.</p>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'jugador-documentacion-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'idEquipo',
			'value'=>'$data->Equipo ? $data->Equipo->Nombre : "Sin equipo"',
			'filter'=>Equipos::getListEquipo(),
		),
		'Nombre',
		'DNI',
		array(
			'header'=>'DNI',
			'value'=>'$data->fotocopia_dni ? "SI" : "NO"',
		),
		array(
			'header'=>'Certificado',
			'value'=>'$data->certificado ? "SI" : "NO"',
		),
		array(
			'header'=>'Lista firmada',
			'value'=>'$data->firma_lista ? "SI" : "NO"',
		),
		array(
			'header'=>'Declaracion jurada',
			'value'=>'$data->dec_jurada ? "SI" : "NO"',
		),
		array(
			'header'=>'Archivos',
			'value'=>'$data->getCantidadDocumentosLegajo()',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{legajo}',
			'buttons'=>array(
				'legajo'=>array(
					'label'=>'Legajo',
					'url'=>'Yii::app()->createUrl("jugador/legajo", array("id"=>$data->idJugador))',
					'options'=>array('class'=>'btn btn-info btn-xs', 'title'=>'Abrir legajo digital'),
				),
			),
		),
	),
)); ?>
