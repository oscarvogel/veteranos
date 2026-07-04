<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idJugador')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idJugador), array('view', 'id'=>$data->idJugador)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Clase')); ?>:</b>
	<?php echo CHtml::encode($data->Clase); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DNI')); ?>:</b>
	<?php echo CHtml::encode($data->DNI); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquipo')); ?>:</b>
	<?php echo CHtml::encode($data->idEquipo); ?>
	<br />


</div>