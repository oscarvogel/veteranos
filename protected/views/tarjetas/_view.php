<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idTarjeta')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idTarjeta), array('view', 'id'=>$data->idTarjeta)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idFixture')); ?>:</b>
	<?php echo CHtml::encode($data->idFixture); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idJugador')); ?>:</b>
	<?php echo CHtml::encode($data->idJugador); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Amarilla')); ?>:</b>
	<?php echo CHtml::encode($data->Amarilla); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Roja')); ?>:</b>
	<?php echo CHtml::encode($data->Roja); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DesdeFecha')); ?>:</b>
	<?php echo CHtml::encode($data->DesdeFecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('HastaFecha')); ?>:</b>
	<?php echo CHtml::encode($data->HastaFecha); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('Motivo')); ?>:</b>
	<?php echo CHtml::encode($data->Motivo); ?>
	<br />

	*/ ?>

</div>