<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquTorneo')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idEquTorneo), array('view', 'id'=>$data->idEquTorneo)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquipo')); ?>:</b>
	<?php echo CHtml::encode($data->Equipos->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idTorneo')); ?>:</b>
	<?php echo CHtml::encode($data->Torneo->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lista')); ?>:</b>
	<?php echo CHtml::encode($data->lista); ?>
	<br />


</div>