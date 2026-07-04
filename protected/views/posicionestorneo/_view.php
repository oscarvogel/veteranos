<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idPosicionTorneo')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idPosicionTorneo), array('view', 'id'=>$data->idPosicionTorneo)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idTorneo')); ?>:</b>
	<?php echo CHtml::encode($data->idTorneo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquipo')); ?>:</b>
	<?php echo CHtml::encode($data->idEquipo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Posicion')); ?>:</b>
	<?php echo CHtml::encode($data->Posicion); ?>
	<br />


</div>