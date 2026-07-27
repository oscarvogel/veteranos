<?php
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
?>

<?php foreach(array('success'=>'alert alert-success', 'error'=>'alert alert-danger') as $tipo=>$clase): ?>
	<?php if(Yii::app()->user->hasFlash($tipo)): ?>
		<div class="<?php echo $clase; ?>">
			<?php echo Yii::app()->user->getFlash($tipo); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

<p><strong><?php echo CHtml::encode($nombreLocal); ?></strong> vs <strong><?php echo CHtml::encode($nombreVisitante); ?></strong></p>

<?php echo CHtml::beginForm(Yii::app()->createUrl('posiciones/detalleGoles', array('idFixture'=>$fixture->idFixture)), 'post', array('class'=>'resultado-modal-form')); ?>
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
			<td><?php echo CHtml::dropDownList('Resultado[Goles]['.$i.'][idJugador]', $gol ? $gol->idJugador : '', $jugadoresPartido, array('prompt'=>'', 'class'=>'form-control')); ?></td>
			<td><?php echo CHtml::textField('Resultado[Goles]['.$i.'][Cantidad]', $gol ? $gol->Cantidad : '', array('maxlength'=>2, 'class'=>'form-control', 'style'=>'width:70px;')); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<div class="text-right">
	<button type="submit" class="btn btn-primary">Guardar goles</button>
</div>
<?php echo CHtml::endForm(); ?>
