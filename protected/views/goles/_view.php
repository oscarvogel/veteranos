<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idGol')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idGol), array('view', 'id'=>$data->idGol)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idJugador')); ?>:</b>
	<?php echo CHtml::encode($data->idJugador); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idFixture')); ?>:</b>
	<?php echo CHtml::encode($data->idFixture); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Cantidad')); ?>:</b>
	<?php echo CHtml::encode($data->Cantidad); ?>
	<br />


</div>