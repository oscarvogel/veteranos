<?php
$this->breadcrumbs=array(
	'Jugadors'=>array('index'),
	$model->idJugador=>array('view','id'=>$model->idJugador),'Actualizar',
);

$this->menu=array(
	array('label'=>'Listar Jugador', 'url'=>array('index')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
	array('label'=>'Ver Jugador', 'url'=>array('view', 'id'=>$model->idJugador)),
	array('label'=>'Administrar Jugador', 'url'=>array('admin')),
);
?>

<h1>Actualizar Jugador <?php echo $model->idJugador; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>