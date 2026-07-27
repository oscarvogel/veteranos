<?php
/* @var $this PosicionesController */

$this->breadcrumbs=array(
	'Posiciones'=>array('resultados'),
	'Detalle Resultado',
);

$nombreLocal = $fixture->local !== null ? $fixture->local->Nombre : 'Libre';
$nombreVisitante = $fixture->visitante !== null ? $fixture->visitante->Nombre : 'Libre';
$jugadoresPartido = array();

if((int)$fixture->Local > 0 && $fixture->local !== null)
	$jugadoresPartido[$nombreLocal] = Jugador::getListJugador($fixture->Local, $fixture->Local, $torneo);
if((int)$fixture->Visitante > 0 && $fixture->visitante !== null)
	$jugadoresPartido[$nombreVisitante] = Jugador::getListJugador($fixture->Visitante, $fixture->Visitante, $torneo);

$criteriaGoles = new CDbCriteria;
$criteriaGoles->condition = 'idFixture=:idFixture';
$criteriaGoles->params = array(':idFixture'=>$fixture->idFixture);
$criteriaGoles->order = 'idGol';
$golesPartido = Goles::model()->findAll($criteriaGoles);
$cantidadFilasGoles = max(count($golesPartido) + 3, 4);

$criteriaTarjetas = new CDbCriteria;
$criteriaTarjetas->condition = 'idFixture=:idFixture';
$criteriaTarjetas->params = array(':idFixture'=>$fixture->idFixture);
$criteriaTarjetas->order = 'idTarjeta';
$tarjetasPartido = Tarjetas::model()->findAll($criteriaTarjetas);
$cantidadFilasTarjetas = max(count($tarjetasPartido) + 3, 4);
?>

<?php foreach(array('success'=>'alert alert-success', 'error'=>'alert alert-danger') as $tipo=>$clase): ?>
	<?php if(Yii::app()->user->hasFlash($tipo)): ?>
		<div class="<?php echo $clase; ?>">
			<?php echo Yii::app()->user->getFlash($tipo); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

<h3><?php echo CHtml::encode($nombreLocal); ?> vs <?php echo CHtml::encode($nombreVisitante); ?></h3>
<p>Fecha <?php echo CHtml::encode($fixture->NFecha); ?> - <?php echo CHtml::encode($fixture->Fecha); ?></p>

<?php echo CHtml::beginForm($this->createUrl('posiciones/detalleResultado', array('idFixture'=>$fixture->idFixture)), 'post'); ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Local</th>
			<th>Gol</th>
			<th>Visitante</th>
			<th>Gol</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo CHtml::encode($nombreLocal); ?></td>
			<td><?php echo CHtml::textField('Resultado[GolLocal]', $fixture->GolLocal, array('maxlength'=>2, 'style'=>'width:55px;')); ?></td>
			<td><?php echo CHtml::encode($nombreVisitante); ?></td>
			<td><?php echo CHtml::textField('Resultado[GolVisitante]', $fixture->GolVisitante, array('maxlength'=>2, 'style'=>'width:55px;')); ?></td>
		</tr>
	</tbody>
</table>

<h4>Goleadores</h4>
<table class="table table-condensed">
	<thead>
		<tr>
			<th>Jugador</th>
			<th>Cantidad</th>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $i < $cantidadFilasGoles; $i++){
			$gol = isset($golesPartido[$i]) ? $golesPartido[$i] : null;
		?>
		<tr>
			<td><?php echo CHtml::dropDownList('Resultado[Goles]['.$i.'][idJugador]', $gol ? $gol->idJugador : '', $jugadoresPartido, array('prompt'=>'')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Goles]['.$i.'][Cantidad]', $gol ? $gol->Cantidad : '', array('maxlength'=>2, 'style'=>'width:55px;')); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<h4>Tarjetas</h4>
<table class="table table-condensed">
	<thead>
		<tr>
			<th>Jugador</th>
			<th>Amarilla</th>
			<th>Roja</th>
			<th>Desde</th>
			<th>Hasta</th>
			<th>Motivo</th>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $i < $cantidadFilasTarjetas; $i++){
			$tarjeta = isset($tarjetasPartido[$i]) ? $tarjetasPartido[$i] : null;
		?>
		<tr>
			<td><?php echo CHtml::dropDownList('Resultado[Tarjetas]['.$i.'][idJugador]', $tarjeta ? $tarjeta->idJugador : '', $jugadoresPartido, array('prompt'=>'')); ?></td>
			<td><?php echo CHtml::checkBox('Resultado[Tarjetas]['.$i.'][Amarilla]', $tarjeta ? (bool)$tarjeta->Amarilla : false, array('uncheckValue'=>null)); ?></td>
			<td><?php echo CHtml::checkBox('Resultado[Tarjetas]['.$i.'][Roja]', $tarjeta ? (bool)$tarjeta->Roja : false, array('uncheckValue'=>null)); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][DesdeFecha]', $tarjeta ? $tarjeta->DesdeFecha : '', array('maxlength'=>2, 'style'=>'width:45px;')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][HastaFecha]', $tarjeta ? $tarjeta->HastaFecha : '', array('maxlength'=>3, 'style'=>'width:55px;')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][Motivo]', $tarjeta ? $tarjeta->Motivo : '', array('style'=>'width:95%;')); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Guardar detalle')); ?>
	<?php echo CHtml::link('Volver a resultados', array('posiciones/resultados'), array('class'=>'btn')); ?>
</div>

<?php echo CHtml::endForm(); ?>
