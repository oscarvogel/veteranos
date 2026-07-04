<?php
$this->breadcrumbs=array(
	'Equiposes'=>array('index'),
	$model->idEquipo,
);

$this->menu=array(
	array('label'=>'Listar Equipos', 'url'=>array('index')),
	array('label'=>'Crear Equipos', 'url'=>array('create')),
	array('label'=>'Actualizar Equipos', 'url'=>array('update', 'id'=>$model->idEquipo)),
	array('label'=>'Borrar Equipos', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->idEquipo),'confirm'=>'Estas seguro que quieres borrar este item')),
	array('label'=>'Administrar Equipos', 'url'=>array('admin')),
);
?>

<h1><?php echo $model->Nombre; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'idEquipo',
		'Nombre',
		'Delegado',
		'DelegadoSuplente',
		'Camiseta',
		'CamisetaSuplente',
		array(
			'name'=>'Cancha',
			'value'=>$model->cancha->Nombre,
		),
		'Correo',
		'Telefono',
	),
)); ?>
<h3>Jugadores del club</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nombre</th>
			<th>DNI</th>
			<th>Clase</th>
		</tr>
	</thead>
<?php 
$jugadores = $jugador->findAll('idEquipo=:idEquipo', array(':idEquipo'=>$model->idEquipo));

foreach ($jugadores as $jug) {?>
	<tr>
		<td><?php echo $jug->Nombre;?></td>
		<td><?php echo $jug->DNI;?></td>
		<td><?php echo $jug->Clase;?></td>
	</tr>
<?php }
?>

</table>