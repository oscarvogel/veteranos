<?php
$this->breadcrumbs=array(
	'Recibos'=>array('admin'),
	$model->NumeroRecibo=>array('view','id'=>$model->idIngreso),
	'Anular',
);
$this->menu=array(
	array('label'=>'Ver Recibo', 'url'=>array('view', 'id'=>$model->idIngreso)),
	array('label'=>'Administrar Recibos', 'url'=>array('admin')),
);
?>

<h1>Anular recibo #<?php echo CHtml::encode($model->NumeroRecibo); ?></h1>

<?php if($model->Estado === 'ANULADO'): ?>
	<div class="alert alert-warning">Este recibo ya esta anulado.</div>
<?php else: ?>
	<form method="post" class="well">
		<label for="motivo">Motivo de anulacion</label>
		<textarea id="motivo" name="motivo" class="span6" rows="4" required></textarea>
		<div class="form-actions">
			<button type="submit" class="btn btn-danger">Anular recibo</button>
			<a class="btn" href="<?php echo Yii::app()->createUrl('ingresos/view', array('id'=>$model->idIngreso)); ?>">Cancelar</a>
		</div>
	</form>
<?php endif; ?>
