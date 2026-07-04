<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'CambiaFechaFixtureForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="row">
	<?php echo $form->dropDownListRow($fixture, 'idTorneo', Torneo::getListTorneo());?>
</div>
<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$fixture,
				'valor'=>'Fecha',
			));?>
</div>
<div class="row">
	<?php echo $form->textFieldRow($fixture,'NFecha',array('size'=>2,'maxlength'=>2)); ?>
</div>
<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$fixture,
				'valor'=>'CambiaFecha',
			));?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Cambia Fecha')); ?>
</div>
<?php $this->endWidget();?>
