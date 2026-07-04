<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEgreso')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idEgreso), array('view', 'id'=>$data->idEgreso)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idConcepto')); ?>:</b>
	<?php echo CHtml::encode($data->idConcepto); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Detalle')); ?>:</b>
	<?php echo CHtml::encode($data->Detalle); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Fecha')); ?>:</b>
	<?php echo CHtml::encode($data->Fecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Monto')); ?>:</b>
	<?php echo CHtml::encode($data->Monto); ?>
	<br />


</div>