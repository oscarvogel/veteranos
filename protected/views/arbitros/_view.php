<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idArbitro')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idArbitro), array('view', 'id'=>$data->idArbitro)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Telefono')); ?>:</b>
	<?php echo CHtml::encode($data->Telefono); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Correo')); ?>:</b>
	<?php echo CHtml::encode($data->Correo); ?>
	<br />


</div>