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
      'value'=>'$data->local->Nombre',
      'filter'=>Equipos::getListEquipo(),
      'type'=>'raw'
    ),
    array(
      'name'=>'Visitante',
      'value'=>'$data->visitante->Nombre',
      'filter'=>Equipos::getListEquipo(),
      'type'=>'raw',
    ),
    array(
      'name'=>'Gol Local',
      'value'=>'$data->GolLocal',
    ),
    array(
      'name'=>'Gol Visitante',
      'value'=>'$data->GolVisitante',
    ),

    array(
      'class'=>'bootstrap.widgets.TbButtonColumn',
        'buttons'=>array(       
         'update' => array(
                       'url'=>'Yii::app()->createUrl("fixture/update", array("id"=>$data->idFixture))',
                       'options'=>array("target"=>"_blank")
            ),)
    ),
  ),
  )); 

}
?>
