<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idIngreso')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idIngreso), array('view', 'id'=>$data->idIngreso)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquipo')); ?>:</b>
	<?php echo CHtml::encode($data->idEquipo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('NFecha')); ?>:</b>
	<?php echo CHtml::encode($data->NFecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Fecha')); ?>:</b>
	<?php echo CHtml::encode($data->Fecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Hora')); ?>:</b>
	<?php echo CHtml::encode($data->Hora); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Monto')); ?>:</b>
	<?php echo CHtml::encode($data->Monto); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idConcepto')); ?>:</b>
	<?php echo CHtml::encode($data->idConcepto); ?>
	<br />


</div>