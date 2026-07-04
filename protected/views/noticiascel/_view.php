<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('idnoticiacel')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->idnoticiacel), array('view', 'id'=>$data->idnoticiacel)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('noticia')); ?>:</b>
	<?php echo CHtml::encode($data->noticia); ?>
	<br />


</div>