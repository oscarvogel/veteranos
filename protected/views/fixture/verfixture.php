<?php
	$i = 0;
	foreach ($fixture as $partido) {
		if ($i == 1){
			$i = 0;
		}else{
			echo '<div class="bloque span3">';
			$i = 1;
		}
		echo ($i == 1 ? '<span>Fecha ' . $partido->NFecha . '</span>' : '');
		echo CHtml::openTag('ul',array('class'=>'tablero'));
		echo '<li>' . CHtml::image(Yii::app()->baseUrl . $partido->local->Escudo, $partido->local->Nombre, array('width'=>43, 'height'=>56)) . '</li>';
		echo '<li>' . CHtml::image(Yii::app()->baseUrl . $partido->visitante->Escudo, $partido->visitante->Nombre, array('width'=>43, 'height'=>56)) . '</li></ul>';
		echo ($i == 0 ? '</div>' : '' );
	}
?>
