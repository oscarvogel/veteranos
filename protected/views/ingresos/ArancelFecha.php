<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'ingresos-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
)); ?>

	<?php echo $form->errorSummary($ingresos); ?>

    <div class="row">
		<?php $this->widget('ext.metro.fieldDateRow',array(
					'model'=>$ingresos,
					'valor'=>'Fecha',
				)); 
			?>
	</div>
    
	<div class="row buttons">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Consultar' )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<?php 
if(isset($dataProvider)){
	$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'ArancelFecha-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'Equipo',
			'value'=>'$data->Equipos->Nombre',
			'filter'=>Equipos::getListEquipo(),
			'type'=>'raw'
		),
		array(
			'name'=>'Concepto',
			'value'=>'$data->Conceptos->Nombre',
			'type'=>'raw'
		),
		 array( 
              'name' => 'Monto',
              'value'=>'$data->Monto',
         ),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
	)); 
}
?>
