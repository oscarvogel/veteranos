<div class="span4">
	<div class="view" <?php echo (Yii::app()->params['EsMovil']) ? 'data-role="collapsible"' : '' ;?>>
		<div class="articulo">
            <h2>
                <?php echo CHtml::encode($data->Titulo); ?>
            </h2>
        
            <?php echo CHtml::decode($data->Introduccion); ?>
            <br />
        
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'label'=>'Leer Mas...',
                'type'=>'success', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                'url'=>Yii::app()->createUrl('/articulos/verArticulo',array('id'=>$data->idArticulo))
            )); ?>
        </div>
    </div>
</div>
