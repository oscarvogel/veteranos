<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'CopiaFixtureFixtureForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<div class="row">
	<?php echo $form->dropDownListRow($fixture, 'idTorneo', Torneo::getListTorneo());?>
</div>

<div class="row">
	<?php echo $form->dropDownListRow($fixture, 'idTorneo', Torneo::getListTorneo(),array('name'=>'idTorneoDestino'));?>
</div>

<div class="row">
	<?php echo $form->textFieldRow($fixture,'NFecha',array('size'=>2,'maxlength'=>2,'name'=>'DesdeFecha')); ?>
</div>

<div class="row">
	<?php echo $form->textFieldRow($fixture,'NFecha',array('size'=>2,'maxlength'=>2,'name'=>'HastaFecha')); ?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Copia Fixture')); ?>
</div>
<?php $this->endWidget();?>
