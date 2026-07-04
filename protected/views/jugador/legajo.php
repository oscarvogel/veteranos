<?php
$this->breadcrumbs=array(
	'Jugadores'=>array('admin'),
	'Legajo digital',
);

$this->menu=array(
	array('label'=>'Administrar Jugadores', 'url'=>array('admin')),
	array('label'=>'Editar Jugador', 'url'=>array('update', 'id'=>$jugador->idJugador)),
	array('label'=>'Documentacion', 'url'=>array('documentacion')),
);

Yii::app()->clientScript->registerCss('legajoJugadorCss', '
	.legajo-header {
		margin-bottom: 18px;
		padding: 16px 18px;
		border: 1px solid #dbe3ee;
		border-radius: 6px;
		background: #fff;
	}
	.legajo-header h1 {
		margin: 0 0 10px;
		font-size: 24px;
	}
	.legajo-meta {
		display: grid;
		grid-template-columns: repeat(4, minmax(120px, 1fr));
		gap: 10px 16px;
		margin: 0;
	}
	.legajo-meta dt {
		color: #526070;
		font-size: 12px;
	}
	.legajo-meta dd {
		margin: 2px 0 0;
		font-weight: bold;
	}
	.legajo-panel {
		margin-bottom: 18px;
		padding: 18px;
		border: 1px solid #dbe3ee;
		border-radius: 6px;
		background: #fff;
	}
	.legajo-panel h2 {
		margin: 0 0 14px;
		font-size: 18px;
	}
	.legajo-form-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(220px, 1fr));
		gap: 14px 18px;
	}
	.legajo-form-field label {
		display: block;
		margin-bottom: 5px;
		font-weight: bold;
	}
	.legajo-form-field input[type="text"],
	.legajo-form-field select,
	.legajo-form-field textarea {
		width: 100%;
		box-sizing: border-box;
	}
	.legajo-form-wide {
		grid-column: 1 / -1;
	}
	.legajo-actions-inline form {
		display: inline;
		margin: 0;
	}
	@media (max-width: 760px) {
		.legajo-meta,
		.legajo-form-grid {
			grid-template-columns: 1fr;
		}
	}
');
?>

<?php foreach(array('success'=>'success', 'error'=>'danger') as $flash=>$class): ?>
	<?php if(Yii::app()->user->hasFlash($flash)): ?>
		<div class="alert alert-<?php echo $class; ?>">
			<?php echo Yii::app()->user->getFlash($flash); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

<div class="legajo-header">
	<h1>Legajo digital</h1>
	<dl class="legajo-meta">
		<div>
			<dt>Jugador</dt>
			<dd><?php echo CHtml::encode($jugador->Nombre); ?></dd>
		</div>
		<div>
			<dt>DNI</dt>
			<dd><?php echo CHtml::encode($jugador->DNI); ?></dd>
		</div>
		<div>
			<dt>Clase</dt>
			<dd><?php echo CHtml::encode($jugador->Clase); ?></dd>
		</div>
		<div>
			<dt>Equipo</dt>
			<dd><?php echo $jugador->Equipo ? CHtml::encode($jugador->Equipo->Nombre) : 'Sin equipo'; ?></dd>
		</div>
	</dl>
</div>

<div class="legajo-panel">
	<h2>Cargar documento</h2>
	<?php echo CHtml::beginForm(array('jugador/subirDocumento', 'id'=>$jugador->idJugador), 'post', array('enctype'=>'multipart/form-data')); ?>
		<div class="legajo-form-grid">
			<div class="legajo-form-field">
				<?php echo CHtml::label('Tipo de documento', 'JugadorDocumento_tipo'); ?>
				<?php echo CHtml::dropDownList('JugadorDocumento[tipo]', '', JugadorDocumento::getTipos(), array('id'=>'JugadorDocumento_tipo', 'empty'=>'Seleccione...')); ?>
			</div>
			<div class="legajo-form-field">
				<?php echo CHtml::label('Titulo', 'JugadorDocumento_titulo'); ?>
				<?php echo CHtml::textField('JugadorDocumento[titulo]', '', array('id'=>'JugadorDocumento_titulo', 'maxlength'=>120)); ?>
			</div>
			<div class="legajo-form-field legajo-form-wide">
				<?php echo CHtml::label('Archivo PDF o imagen', 'archivo'); ?>
				<?php echo CHtml::fileField('archivo', '', array('id'=>'archivo', 'accept'=>'.pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png')); ?>
				<p class="help-block">Formatos permitidos: PDF, JPG, JPEG, PNG. Maximo 10 MB.</p>
			</div>
			<div class="legajo-form-field legajo-form-wide">
				<?php echo CHtml::label('Observacion', 'JugadorDocumento_observacion'); ?>
				<?php echo CHtml::textArea('JugadorDocumento[observacion]', '', array('id'=>'JugadorDocumento_observacion', 'rows'=>3, 'maxlength'=>500)); ?>
			</div>
		</div>
		<div class="form-actions">
			<?php echo CHtml::submitButton('Subir documento', array('class'=>'btn btn-primary')); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>

<div class="legajo-panel">
	<h2>Documentos cargados</h2>
	<?php if(empty($documentos)): ?>
		<p>No hay documentos cargados para este jugador.</p>
	<?php else: ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Titulo</th>
					<th>Archivo</th>
					<th>Tamanio</th>
					<th>Fecha</th>
					<th>Observacion</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($documentos as $doc): ?>
					<tr>
						<td><?php echo CHtml::encode($doc->getTipoLabel()); ?></td>
						<td><?php echo CHtml::encode($doc->titulo); ?></td>
						<td><?php echo CHtml::encode($doc->archivo_original); ?></td>
						<td><?php echo CHtml::encode($doc->getTamanoLegible()); ?></td>
						<td><?php echo CHtml::encode(date('d/m/Y H:i', strtotime($doc->created_at))); ?></td>
						<td><?php echo CHtml::encode($doc->observacion); ?></td>
						<td class="legajo-actions-inline">
							<?php echo CHtml::link('Descargar', array('jugador/descargarDocumento', 'idDocumento'=>$doc->idDocumento), array('class'=>'btn btn-info btn-xs')); ?>
							<?php if($puedeEliminar): ?>
								<?php echo CHtml::beginForm(array('jugador/eliminarDocumento', 'idDocumento'=>$doc->idDocumento), 'post'); ?>
									<?php echo CHtml::submitButton('Eliminar', array('class'=>'btn btn-danger btn-xs', 'onclick'=>'return confirm("Eliminar este documento del legajo?");')); ?>
								<?php echo CHtml::endForm(); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
