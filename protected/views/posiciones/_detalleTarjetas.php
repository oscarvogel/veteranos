<?php
$nombreLocal = $fixture->local !== null ? $fixture->local->Nombre : 'Libre';
$nombreVisitante = $fixture->visitante !== null ? $fixture->visitante->Nombre : 'Libre';
$jugadoresPartido = array();

if((int)$fixture->Local > 0 && $fixture->local !== null)
	$jugadoresPartido[$nombreLocal] = Jugador::getListJugador($fixture->Local, $fixture->Local, $torneo);
if((int)$fixture->Visitante > 0 && $fixture->visitante !== null)
	$jugadoresPartido[$nombreVisitante] = Jugador::getListJugador($fixture->Visitante, $fixture->Visitante, $torneo);

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

<p><strong><?php echo CHtml::encode($nombreLocal); ?></strong> vs <strong><?php echo CHtml::encode($nombreVisitante); ?></strong></p>

<?php echo CHtml::beginForm(Yii::app()->createUrl('posiciones/detalleTarjetas', array('idFixture'=>$fixture->idFixture)), 'post', array('class'=>'resultado-modal-form')); ?>
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
			<td><?php echo CHtml::dropDownList('Resultado[Tarjetas]['.$i.'][idJugador]', $tarjeta ? $tarjeta->idJugador : '', $jugadoresPartido, array('prompt'=>'', 'class'=>'form-control')); ?></td>
			<td><?php echo CHtml::checkBox('Resultado[Tarjetas]['.$i.'][Amarilla]', $tarjeta ? (bool)$tarjeta->Amarilla : false, array('uncheckValue'=>null)); ?></td>
			<td><?php echo CHtml::checkBox('Resultado[Tarjetas]['.$i.'][Roja]', $tarjeta ? (bool)$tarjeta->Roja : false, array('uncheckValue'=>null)); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][DesdeFecha]', $tarjeta ? $tarjeta->DesdeFecha : '', array('maxlength'=>2, 'class'=>'form-control', 'style'=>'width:60px;')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][HastaFecha]', $tarjeta ? $tarjeta->HastaFecha : '', array('maxlength'=>3, 'class'=>'form-control', 'style'=>'width:70px;')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Tarjetas]['.$i.'][Motivo]', $tarjeta ? $tarjeta->Motivo : '', array('class'=>'form-control')); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<div class="text-right">
	<button type="submit" class="btn btn-primary">Guardar tarjetas</button>
</div>
<?php echo CHtml::endForm(); ?>
