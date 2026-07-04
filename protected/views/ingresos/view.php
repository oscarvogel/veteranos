<?php
$this->breadcrumbs=array(
	'Recibos'=>array('admin'),
	$model->NumeroRecibo,
);

$this->menu=array(
	array('label'=>'Registrar Pago', 'url'=>array('create')),
	array('label'=>'Imprimir PDF', 'url'=>array('reciboPdf', 'id'=>$model->idIngreso), 'linkOptions'=>array('target'=>'_blank')),
	array('label'=>'Enviar por WhatsApp', 'url'=>$model->getWhatsappUrl(), 'linkOptions'=>array('target'=>'_blank')),
	array('label'=>'Anular Recibo', 'url'=>array('anular', 'id'=>$model->idIngreso), 'visible'=>$model->Estado !== 'ANULADO'),
	array('label'=>'Administrar Recibos', 'url'=>array('admin')),
	array('label'=>'Arqueo de Caja', 'url'=>array('arqueoCaja')),
);
?>

<h1>Recibo #<?php echo CHtml::encode($model->NumeroRecibo); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'NumeroRecibo',
		'Estado',
		array(
			'name'=>'idEquipo',
			'value'=>$model->Equipos->Nombre
		),
		array(
			'name'=>'idConcepto',
			'value'=>$model->Conceptos->Nombre,
		),
		'NFecha',
		array(
			'name'=>'Fecha',
			'value'=> Yii::app()->dateFormatter->formatDateTime(
                    CDateTimeParser::parse(
                        $model->Fecha, 
                        'yyyy-MM-dd'
                    ),
                    'full',null
                ),
		),
		'Hora',
		'Monto',
		'Detalle',
		array(
			'name'=>'idUsuario',
			'value'=>$model->Usuario ? $model->Usuario->username : '',
		),
		'FechaAlta',
		'FechaAnulacion',
		'MotivoAnulacion',
	),
)); ?>
