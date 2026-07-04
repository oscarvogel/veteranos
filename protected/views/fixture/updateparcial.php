<?php $this->beginWidget('bootstrap.widgets.TbModal', 
		array('id'=>'myModal','autoOpen'=>true) ); ?>
	
<?php echo $this->renderPartial('//fixture/_form', array(
			'model'=>$model,
			'goleador'=>$goleador,
        	'validatedMembers' => $validatedMembers,));?>

<?php $this->endWidget(); ?>
