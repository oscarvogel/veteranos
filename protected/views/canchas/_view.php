<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idCancha')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idCancha), array('view', 'id'=>$data->idCancha)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Titular')); ?>:</b>
	<?php echo CHtml::encode($data->Titular); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Telefono')); ?>:</b>
	<?php echo CHtml::encode($data->Telefono); ?>
	<br />


</div>