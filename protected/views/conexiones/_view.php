<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idConexion')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idConexion), array('view', 'id'=>$data->idConexion)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('latitud')); ?>:</b>
	<?php echo CHtml::encode($data->latitud); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('longitud')); ?>:</b>
	<?php echo CHtml::encode($data->longitud); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('altitud')); ?>:</b>
	<?php echo CHtml::encode($data->altitud); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('horario')); ?>:</b>
	<?php echo CHtml::encode($data->horario); ?>
	<br />


</div>