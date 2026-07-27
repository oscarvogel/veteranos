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

<?php foreach(array('success'=>'alert alert-success', 'error'=>'alert alert-danger') as $tipo=>$clase): ?>
	<?php if(Yii::app()->user->hasFlash($tipo)): ?>
		<div class="<?php echo $clase; ?>">
			<?php echo Yii::app()->user->getFlash($tipo); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

<h3>Resultados</h3>
<?php
$puedeEditar = !Yii::app()->user->isGuest && in_array(Yii::app()->user->name, array('admin','oscarvogel'));
$idTorneoActual = $torneo ? $torneo->idTorneo : $model->idTorneo;
$fechaActual = $model->Fecha;

if($puedeEditar && count($datos) > 0){
	Yii::app()->clientScript->registerScript('resultados-rapidos', "
$('.resultado-gol').on('change keyup', function(){
	$('#' + $(this).data('check')).prop('checked', true);
});
$(document).on('click', '.resultado-modal-link', function(e){
	e.preventDefault();
	var link = $(this);
	$('#resultadoDetalleModalLabel').text(link.data('title'));
	$('#resultadoDetalleModal .modal-body').html('<p>Cargando...</p>');
	$('#resultadoDetalleModal').modal('show');
	$('#resultadoDetalleModal .modal-body').load(link.attr('href'));
});
$(document).on('submit', '#resultadoDetalleModal .resultado-modal-form', function(e){
	e.preventDefault();
	var form = $(this);
	$.post(form.attr('action'), form.serialize(), function(html){
		$('#resultadoDetalleModal .modal-body').html(html);
	});
});
");
	echo CHtml::beginForm($this->createUrl('posiciones/guardarResultados'), 'post');
	echo CHtml::hiddenField('idTorneo', $idTorneoActual);
	echo CHtml::hiddenField('Fecha', $fechaActual);
}
?>
<table class="table table-striped">
	<thead>
		<tr>
			<?php if($puedeEditar){ ?><th>Actualizar</th><?php } ?>
			<th>Local</th>
			<th>Gol</th>
			<th>Visitante</th>
			<th>Gol</th>
			<?php if($puedeEditar){ ?><th></th><?php } ?>
		</tr>
	</thead>
<?php
foreach ($datos as $dato) {
	$checkId = 'resultado_' . $dato->idFixture;
	$actualizar = ($dato->PuntosLocal != 0 || $dato->PuntosVisitante != 0 || $dato->GolLocal != 0 || $dato->GolVisitante != 0);
	$nombreLocal = $dato->local !== null ? $dato->local->Nombre : 'Libre';
	$nombreVisitante = $dato->visitante !== null ? $dato->visitante->Nombre : 'Libre';
	?>
	<tr>
		<?php if($puedeEditar){ ?>
			<td><?php echo CHtml::checkBox("Resultado[".$dato->idFixture."][actualizar]", $actualizar, array('id'=>$checkId, 'uncheckValue'=>null)); ?></td>
		<?php } ?>
        <td>
			<?php if((int)$dato->Local > 0 && $dato->local !== null){ ?>
				<a href="<?php echo Yii::app()->createUrl('posiciones/verFichaPartido',
					array('idFixture'=>$dato->idFixture, 'idEquipo'=>$dato->Local, 'idTorneo'=>$idTorneoActual));?>" target="_blank"><?php echo $nombreLocal;?></a>
			<?php }else{ ?>
				<?php echo $nombreLocal;?>
			<?php } ?>
		</td>
        <td>
			<?php if($puedeEditar){ ?>
				<?php echo CHtml::textField("Resultado[".$dato->idFixture."][GolLocal]", $dato->GolLocal, array('class'=>'resultado-gol input-mini', 'maxlength'=>2, 'style'=>'width:45px;', 'data-check'=>$checkId)); ?>
			<?php }else{ ?>
				<?php echo $dato->GolLocal;?>
			<?php } ?>
		</td>
        <td>
			<?php if((int)$dato->Visitante > 0 && $dato->visitante !== null){ ?>
				<a href="<?php echo Yii::app()->createUrl('posiciones/verFichaPartido',
					array('idFixture'=>$dato->idFixture, 'idEquipo'=>$dato->Visitante, 'idTorneo'=>$idTorneoActual));?>" target="_blank"><?php echo $nombreVisitante;?></a>
			<?php }else{ ?>
				<?php echo $nombreVisitante;?>
			<?php } ?>
		</td>
        <td>
			<?php if($puedeEditar){ ?>
				<?php echo CHtml::textField("Resultado[".$dato->idFixture."][GolVisitante]", $dato->GolVisitante, array('class'=>'resultado-gol input-mini', 'maxlength'=>2, 'style'=>'width:45px;', 'data-check'=>$checkId)); ?>
			<?php }else{ ?>
				<?php echo $dato->GolVisitante;?>
			<?php } ?>
		</td>
		<?php if($puedeEditar){ ?>
			<td>
				<a href="<?php echo Yii::app()->createUrl('posiciones/detalleGoles', array('idFixture'=>$dato->idFixture));?>" class="resultado-modal-link" data-title="Goles">Goles</a>
				&nbsp;|&nbsp;
				<a href="<?php echo Yii::app()->createUrl('posiciones/detalleTarjetas', array('idFixture'=>$dato->idFixture));?>" class="resultado-modal-link" data-title="Tarjetas">Tarjetas</a>
				<br>
				<a href="<?php echo Yii::app()->createUrl('fixture/update', array('id'=>$dato->idFixture));?>" target="_blank">Editar completo</a>
			</td>
		<?php } ?>
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
<?php if($puedeEditar && count($datos) > 0){ ?>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Guardar resultados y puntos', 'htmlOptions'=>array('class' => 'button primary'))); ?>
	</div>
	<p class="help-block">Para un 0-0 real, marque Actualizar antes de guardar.</p>
<?php echo CHtml::endForm(); ?>
<div class="modal fade" id="resultadoDetalleModal" tabindex="-1" role="dialog" aria-labelledby="resultadoDetalleModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="resultadoDetalleModalLabel">Detalle</h4>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>
<?php } ?>
