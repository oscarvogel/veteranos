<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'consultaFixtureForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$fixture,
				'valor'=>'Fecha',
			)); 

		?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consulta Fecha','htmlOptions'=>array('class' => 'button primary'))); ?>
</div>
<?php $this->endWidget();?>


<?php 
if(isset($dataProvider)){
	$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'fixture-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'Local',
			'value'=>'CHtml::link($data->local->Nombre,array("fixture/verCanchas","idEquipo"=>$data->Local,"idTorneo"=>$data->idTorneo),array("target"=>"_blank"))',
			'filter'=>Equipos::getListEquipo(),
			'type'=>'raw'
		),
		array(
			'name'=>'Visitante',
			'value'=>'CHtml::link($data->visitante->Nombre,array("fixture/verCanchas","idEquipo"=>$data->Visitante, "idTorneo"=>$data->idTorneo),array("target"=>"_blank"))',
			'filter'=>Equipos::getListEquipo(),
			'type'=>'raw',
		),
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idCancha',
              'value'=>'$data->Cancha->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/actualiza'),
                  'source'   => Yii::app()->createUrl('//canchas/GetCanchas'),
                   //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idArbitro',
              'value'=>'$data->Arbitro->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/actualiza'),
                  'source'   => Yii::app()->createUrl('//arbitros/GetArbitros'),
                   //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idLinea1',
              'value'=>'$data->Linea1->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/actualiza'),
                  'source'   => Yii::app()->createUrl('//arbitros/GetArbitros'),
                   //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'idLinea2',
              'value'=>'$data->Linea2->Nombre',
              'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/actualiza'),
                  'source'   => Yii::app()->createUrl('//arbitros/GetArbitros'),
                   //onsave event handler 
                 'onSave' => 'js: function(e, params) {
                      console && console.log("saved value: "+params.newValue);
                  }' 
              )
         ),
		 array( 
              'class' => 'editable.EditableColumn',
              'name' => 'Hora',
              'value'=>'$data->Hora',
              'headerHtmlOptions' => array('style' => 'width: 50px'),
              'editable' => array(
              	  'type'=>'select',
                  'url'      => $this->createUrl('fixture/actualiza'),
                  'source'   => Yii::app()->createUrl('//fixture/getHora'),
              )
         ),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
	)); 
}
?>
