<?php
$this->breadcrumbs=array(
	'Jugadores'=>array('admin'),
	'Importar lista buena fe',
);

$this->menu=array(
	array('label'=>'Administrar Jugadores', 'url'=>array('admin')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
);
?>

<h1>Importar lista de buena fe</h1>

<p>
	Seleccione el equipo y suba un archivo CSV o XLSX con columnas como
	<strong>apellido_y_nombre</strong>, <strong>fecha_nacimiento</strong> y <strong>n_doc</strong>.
</p>

<?php if($error !== null): ?>
	<div class="alert alert-error">
		<?php echo CHtml::encode($error); ?>
	</div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?php echo CHtml::encode(Yii::app()->user->getFlash('success')); ?>
	</div>
<?php endif; ?>

<div class="form">
<?php echo CHtml::beginForm(array('jugador/importarListaBuenaFe'), 'post', array('enctype'=>'multipart/form-data')); ?>

	<div class="row">
		<?php echo CHtml::label('Equipo', 'idEquipo'); ?>
		<?php echo CHtml::dropDownList('idEquipo', $idEquipo, Equipos::getListEquipo(), array(
			'prompt'=>'Seleccione un equipo',
			'required'=>'required',
		)); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Archivo CSV o XLSX', 'archivo'); ?>
		<?php echo CHtml::fileField('archivo', '', array(
			'accept'=>'.csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'required'=>'required',
		)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Importar'); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div>

<?php if(is_array($resultado)): ?>
	<h2>Resultado</h2>
	<table class="items table table-striped">
		<tbody>
			<tr><th>Total de filas</th><td><?php echo (int)$resultado['total']; ?></td></tr>
			<tr><th>Jugadores asignados</th><td><?php echo (int)$resultado['asignados']; ?></td></tr>
			<tr><th>Fechas actualizadas</th><td><?php echo (int)$resultado['fechas_actualizadas']; ?></td></tr>
			<tr><th>DNI no encontrados</th><td><?php echo (int)$resultado['no_encontrados']; ?></td></tr>
		</tbody>
	</table>

	<?php if(!empty($resultado['dni_no_encontrados'])): ?>
		<h3>DNI no encontrados</h3>
		<ul>
			<?php foreach($resultado['dni_no_encontrados'] as $dni): ?>
				<li><?php echo CHtml::encode($dni); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if(!empty($resultado['fechas_invalidas'])): ?>
		<h3>Fechas invalidas</h3>
		<p>Estos jugadores fueron asignados al equipo, pero no se cargo la fecha porque el archivo trae una fecha invalida.</p>
		<ul>
			<?php foreach($resultado['fechas_invalidas'] as $dni): ?>
				<li><?php echo CHtml::encode($dni); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php endif; ?>
