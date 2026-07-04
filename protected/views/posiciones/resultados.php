<?php
/* @var $this PosicionesController */

$this->breadcrumbs=array(
	'Posiciones',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'inline',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="form-group">
	<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo('I'),
                                      array('class'=>'form-control')); ?>
</div>
<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$model,
				'valor'=>'Fecha',
			)); 
		?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=> 'primary', 'label'=>'Consultar', 'htmlOptions'=>array('class' => 'button primary'))); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=> 'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('class' => 'button success','name'=>'btnExcel'))); ?>
</div>
<?php $this->endWidget();?>

<h3>Resultados</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Local</th>
			<th>Gol</th>
			<th>Visitante</th>
			<th>Gol</th>
		</tr>
	</thead>
<?php
foreach ($datos as $dato) {
	?>
	<tr>
        <td><a href="<?php echo Yii::app()->createUrl('posiciones/verFichaPartido',
                array('idFixture'=>$dato->idFixture, 'idEquipo'=>$dato->Local, 'idTorneo'=>$torneo->idTorneo));?>" target="_blank"/><?php echo $dato->local->Nombre;?></a></td>
        <td><?php echo $dato->GolLocal;?></td>
        <td><a href="<?php echo Yii::app()->createUrl('posiciones/verFichaPartido',
                array('idFixture'=>$dato->idFixture, 'idEquipo'=>$dato->Visitante, 'idTorneo'=>$torneo->idTorneo));?>" target="_blank"/><?php echo $dato->visitante->Nombre;?></a></td>
        <td><?php echo $dato->GolVisitante;?></td>
	</tr>
	<tr>
		<td><a href="<?php echo Yii::app()->baseUrl;?><?php echo $dato->Archivo;?>" class="button samall success" target="_blank"/>Planilla partido</a></td>
		<td></td><td></td><td></td>
	</tr>
    <?php /*<tr>
    	<td><?php echo Goles::model()->golPartido($dato->idFixture,$dato->Local,$torneo);?></td>
        <td></td>
        <td><?php echo Goles::model()->golPartido($dato->idFixture,$dato->Visitante,$torneo);?></td>
        <td></td>
    </tr>*/?>
<?php } 
?>
</table>