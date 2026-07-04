<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idConcepto')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idConcepto), array('view', 'id'=>$data->idConcepto)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Monto')); ?>:</b>
	<?php echo CHtml::encode($data->Monto); ?>
	<br />


</div>