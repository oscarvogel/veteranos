<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idFixture')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idFixture), array('view', 'id'=>$data->idFixture)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idTorneo')); ?>:</b>
	<?php echo CHtml::encode($data->Torneo->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('NFecha')); ?>:</b>
	<?php echo CHtml::encode($data->NFecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Fecha')); ?>:</b>
	<?php echo CHtml::encode($data->Fecha); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Local')); ?>:</b>
	<?php echo CHtml::encode($data->local->Nombre) . " (" . CHtml::encode($data->GolLocal) . ")"; ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Visitante')); ?>:</b>
	<?php echo CHtml::encode($data->visitante->Nombre) . " (" . CHtml::encode($data->GolVisitante) . ")"; ?>
	<br />

</div>