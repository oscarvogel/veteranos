<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idEquipo')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idEquipo), array('view', 'id'=>$data->idEquipo)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Nombre')); ?>:</b>
	<?php echo CHtml::encode($data->Nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Delegado')); ?>:</b>
	<?php echo CHtml::encode($data->Delegado); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('idCategoria')); ?>:</b>
	<?php echo CHtml::encode($data->Categoria->Nombre); ?>
	<br />


</div>