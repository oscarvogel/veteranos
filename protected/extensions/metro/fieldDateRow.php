<?php

class fieldDateRow extends CWidget{

	public $valor;
	
	public $model; 
	
	public $cs;

	public function run(){

		$id = CHtml::activeId($this->model, $this->valor);
		$cs = Yii::app()->clientScript;
		$cs->registerScript('btnDateTrigger'.$id, "
			$('#{$id}').parent('.input-control').find('.btn-date').on('click',function(e){
				e.preventDefault();
				var \$i = $('#{$id}');
				if (!\$i.data('ui-datepicker')) {
					\$i.datepicker({
						dateFormat: 'yy-mm-dd',
						changeMonth: true,
						changeYear: true
					});
				}
				\$i.datepicker('show');
			});
		", CClientScript::POS_READY);

		echo '<div class="control-group">';
		echo CHtml::activeLabelEx($this->model, $this->valor, array('class'=>'control-label')) ;?>
		<div class="input-control text" data-role="datepicker"
			data-format="yyyy-mm-dd"
			data-locale='es'
			data-position="top|bottom"
			data-effect='slide | fade | none'
			data-week-start='0 | 1'
			data-other-days='0 | 1'>
		<?php
		echo CHtml::activeTextField($this->model, $this->valor) ;
		echo '<button class="btn-date"></button>';
		echo '</div></div>';
	}

}

?>