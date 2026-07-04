<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idTorneo')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idTorneo), array('view', 'id'=>$data->idTorneo)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Inicio')); ?>:</b>
	<?php echo CHtml::encode($data->Inicio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Estado')); ?>:</b>
	<?php echo CHtml::encode($data->Estado); ?>
	<br />


</div>