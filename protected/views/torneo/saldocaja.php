<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'SaldoCajaForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
			'model'=>$torneo,
			'valor'=>'DesdeFecha',
		));?>

</div>

<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
			'model'=>$torneo,
			'valor'=>'HastaFecha',
		));?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consulta Saldo')); ?>
</div>

<?php $this->endWidget();?>

<table class="table">
	<thead>
		<th>Concepto</th>
		<th>Debe</th>
		<th>Haber</th>
		<th>Saldo</th>
	</thead>
<?php 
$saldo = 0.00;
if(isset($ingresos)){
	//print_r($ingresos);
	foreach ($ingresos as $ingreso) {?>
		
	<tr class="success">
		<td><a href="<?php echo Yii::app()->createUrl('/conceptos/ConsultaMovimientos',array('idConcepto'=>$ingreso->idConcepto,'tipo'=>'I'));?>"/><?php echo $ingreso->Conceptos->Nombre;?></a></td>
		<td></td>
		<td><?php echo $ingreso->Monto;?></td>
		<td><?php echo $saldo+=$ingreso->Monto;?></td>
	</tr>

<?php }
}?>

<?php if(isset($egresos)){
	//print_r($ingresos);
	foreach ($egresos as $egreso) {?>
		
	<tr class="error">
		<td><a href="<?php echo Yii::app()->createUrl('/conceptos/ConsultaMovimientos',array('idConcepto'=>$egreso->idConcepto,'tipo'=>'E'));?>"/><?php echo $egreso->Conceptos->Nombre;?></a></td>
		<td><?php echo $egreso->Monto;?></td>
		<td></td>
		<td><?php echo $saldo-=$egreso->Monto;?></td>
	</tr>

<?php }
}?>
<tr class="<?php echo ($saldo > 0) ? 'success' : 'error';?>">
	<td>Saldo Final</td>
	<td></td>
	<td></td>
	<td><?php echo $saldo;?></td>
</tr>
</table>