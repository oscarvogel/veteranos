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
	<?php echo $form->textFieldRow($fixture,'NFecha',array('size'=>2,'maxlength'=>2)); ?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Adelanta Fecha')); ?>
</div>
<?php $this->endWidget();?>
