<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Detalle')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Detalle),               
                Yii::app()->baseUrl . '/' . CHtml::encode($data->URL) . '.pdf'); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Fecha')); ?>:</b>
	<?php echo CHtml::encode($data->Fecha); ?>
	<br />

</div>