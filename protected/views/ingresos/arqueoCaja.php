<?php
$this->breadcrumbs=array('Caja'=>'#','Arqueo');
$this->menu=array(
	array('label'=>'Registrar Pago', 'url'=>array('create')),
	array('label'=>'Administrar Recibos', 'url'=>array('admin')),
	array('label'=>'Resumen mensual', 'url'=>array('resumenMensual')),
	array('label'=>'Conceptos de cobro', 'url'=>array('/conceptos/admin')),
);
$usuarios = CHtml::listData(CrugeStoredUser::model()->findAll(array('order'=>'username')), 'iduser', 'username');
?>

<h1>Arqueo de Caja</h1>

<form method="get" class="well form-inline">
	<input type="hidden" name="r" value="ingresos/arqueoCaja">
	<label>Desde</label>
	<input type="date" name="desde" value="<?php echo CHtml::encode($desde); ?>">
	<label>Hasta</label>
	<input type="date" name="hasta" value="<?php echo CHtml::encode($hasta); ?>">
	<label>Cobrador</label>
	<?php echo CHtml::dropDownList('idUsuario', $idUsuario, array(''=>'Todos') + $usuarios); ?>
	<button type="submit" class="btn btn-primary">Consultar</button>
</form>

<h3>Total vigente: $ <?php echo number_format((float)$totalVigente, 2, ',', '.'); ?></h3>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'arqueo-caja-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		'NumeroRecibo',
		'Fecha',
		array('name'=>'Equipo', 'value'=>'$data->Equipos->Nombre'),
		array('name'=>'Concepto', 'value'=>'$data->Conceptos->Nombre'),
		'Monto',
		'Estado',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {pdf}',
			'buttons'=>array(
				'pdf'=>array(
					'label'=>'PDF',
					'imageUrl'=>Yii::app()->baseUrl . '/media/iconos/recibo-pdf.png',
					'url'=>'Yii::app()->createUrl("ingresos/reciboPdf", array("id"=>$data->idIngreso))',
					'options'=>array('target'=>'_blank'),
				),
			),
		),
	),
)); ?>
