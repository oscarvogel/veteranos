<?php
$this->breadcrumbs=array(
	'Fixtures'=>array('index'),
	$model->idFixture,
);

$this->menu=array(
	array('label'=>'Listar Fixture', 'url'=>array('index')),
	array('label'=>'Crear Fixture', 'url'=>array('create')),
	array('label'=>'Actualizar Fixture', 'url'=>array('update', 'id'=>$model->idFixture)),
	array('label'=>'Borrar Fixture', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idFixture),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Fixture', 'url'=>array('admin')),
);
?>

<h1>Resultados Fecha #<?php echo $model->NFecha; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idFixture',
		array(
			'name'=>'Torneo',
			'value'=>$model->Torneo->Nombre
		),
		'NFecha',
		'Fecha',
		array(
			'name'=>'Local',
			'value'=>$model->local->Nombre
		),
		array(
			'name'=>'Visitante',
			'value'=>$model->visitante->Nombre
		),
		'GolLocal',
		'GolVisitante',
	),
)); ?>
<h3>Goles</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Jugador</th>
			<th>Equipo</th>
			<th>Cantidad</th>
		</tr>
	</thead>
<?php
$goles = $goleador->findAll('idFixture=:idFixture', array(':idFixture'=>$model->idFixture));
foreach ($goles as $gol) {?>
	<tr>
		<td><?php echo $gol->Jugador->Nombre;?></td>
		<td><?php echo $gol->Jugador->Equipo->Nombre;?></td>
		<td><?php echo $gol->Cantidad;?></td>
	</tr>
<?php } 
?>
</table>