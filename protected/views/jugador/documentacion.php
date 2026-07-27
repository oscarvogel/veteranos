<?php
$this->breadcrumbs=array(
	'Jugadores'=>array('admin'),
	'Documentacion',
);

$this->menu=array(
	array('label'=>'Administrar Jugadores', 'url'=>array('admin')),
	array('label'=>'Crear Jugador', 'url'=>array('create')),
);

Yii::app()->clientScript->registerCss('jugadorDocumentacionCss', '
	.documentacion-header {
		margin-bottom: 16px;
		padding: 16px 18px;
		border: 1px solid #dbe3ee;
		border-radius: 6px;
		background: #fff;
	}
	.documentacion-header h1 {
		margin: 0 0 6px;
		font-size: 24px;
	}
	.documentacion-header p {
		margin: 0;
		color: #526070;
	}
	.documentacion-panel {
		margin-bottom: 18px;
		padding: 18px;
		border: 1px solid #dbe3ee;
		border-radius: 6px;
		background: #fff;
	}
	.documentacion-panel h2 {
		margin: 0 0 14px;
		font-size: 18px;
	}
	.documentacion-form-grid {
		display: grid;
		grid-template-columns: minmax(260px, 1fr) auto;
		gap: 12px;
		align-items: end;
		max-width: 720px;
	}
	.documentacion-search-grid {
		display: grid;
		grid-template-columns: minmax(180px, 1fr) minmax(120px, 180px) minmax(180px, 260px) auto;
		gap: 12px;
		align-items: end;
	}
	.documentacion-field label {
		display: block;
		margin-bottom: 5px;
		font-weight: bold;
	}
	.documentacion-field input,
	.documentacion-field select {
		width: 100%;
		box-sizing: border-box;
	}
	#jugador-resultados.jugador-resultados {
		width: 520px;
		max-width: 100%;
		margin-top: 8px;
		border: 1px solid #cbd5e1;
		border-radius: 4px;
		background: #fff;
		box-shadow: 0 4px 12px rgba(15,23,42,0.12);
		overflow: hidden;
	}
	#jugador-resultados .jugador-opcion {
		display: block;
		width: 100%;
		padding: 8px 10px;
		border: 0;
		border-bottom: 1px solid #eef2f7;
		background: #fff;
		color: #1f2937;
		text-align: left;
		cursor: pointer;
		font-size: 13px;
		line-height: 1.25;
	}
	#jugador-resultados .jugador-opcion:hover,
	#jugador-resultados .jugador-opcion:focus {
		background: #eef6ff;
		outline: none;
	}
	#jugador-resultados .jugador-opcion:last-child {
		border-bottom: 0;
	}
	#jugador-resultados .jugador-estado,
	.documentacion-empty {
		color: #64748b;
		font-size: 13px;
	}
	#jugador-resultados .jugador-estado {
		padding: 8px 10px;
	}
	#jugador-seleccionado.jugador-seleccionado {
		width: 520px;
		max-width: 100%;
		margin-top: 8px;
		padding: 8px 10px;
		border: 1px solid #bfdbfe;
		border-radius: 4px;
		background: #eff6ff;
		color: #1e3a8a;
		font-size: 13px;
		font-weight: bold;
	}
	.documentacion-status {
		display: inline-block;
		min-width: 22px;
		padding: 2px 6px;
		border-radius: 10px;
		text-align: center;
		font-size: 11px;
		font-weight: bold;
	}
	.documentacion-status-ok {
		background: #e7f6ec;
		color: #216e39;
	}
	.documentacion-status-falta {
		background: #f3f4f6;
		color: #6b7280;
	}
	.documentacion-actions {
		white-space: nowrap;
	}
	@media (max-width: 900px) {
		.documentacion-form-grid,
		.documentacion-search-grid {
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

<div class="documentacion-header">
	<h1>Documentacion de jugadores</h1>
	<p>Seleccione un jugador para abrir su legajo y cargar DNI, certificado, lista firmada, declaracion jurada u otros archivos.</p>
</div>

<div class="documentacion-panel">
	<h2>Seleccionar jugador</h2>
	<?php echo CHtml::beginForm(array('jugador/documentacion'), 'post', array('id'=>'jugador-documentacion-form')); ?>
		<?php echo CHtml::activeHiddenField($jugadores, 'idJugador'); ?>
		<div class="documentacion-form-grid">
			<div class="documentacion-field">
				<?php echo CHtml::activeLabel($jugadores, 'Nombre'); ?>
				<?php echo CHtml::activeTextField($jugadores, 'Nombre', array(
					'id'=>'Jugador_Nombre',
					'autocomplete'=>'off',
					'placeholder'=>'Escriba al menos 2 letras del nombre',
				)); ?>
				<div id="jugador-resultados" class="jugador-resultados" style="display:none;"></div>
				<div id="jugador-seleccionado" class="jugador-seleccionado" style="display:none;"></div>
			</div>
			<div>
				<?php echo CHtml::submitButton('Abrir legajo', array('class'=>'btn btn-primary')); ?>
			</div>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>

<div class="documentacion-panel">
	<h2>Buscar jugador</h2>
	<?php echo CHtml::beginForm(array('jugador/documentacion'), 'get'); ?>
		<div class="documentacion-search-grid">
			<div class="documentacion-field">
				<?php echo CHtml::label('Nombre', 'q'); ?>
				<?php echo CHtml::textField('q', $termino, array('id'=>'q')); ?>
			</div>
			<div class="documentacion-field">
				<?php echo CHtml::label('DNI', 'dni'); ?>
				<?php echo CHtml::textField('dni', $dni, array('id'=>'dni')); ?>
			</div>
			<div class="documentacion-field">
				<?php echo CHtml::label('Equipo', 'idEquipo'); ?>
				<?php echo CHtml::dropDownList('idEquipo', $idEquipo, Equipos::getListEquipo(), array('id'=>'idEquipo', 'empty'=>'Todos')); ?>
			</div>
			<div>
				<?php echo CHtml::submitButton('Buscar', array('class'=>'btn')); ?>
			</div>
		</div>
	<?php echo CHtml::endForm(); ?>

	<?php if($hayBusqueda): ?>
		<?php if(empty($resultados)): ?>
			<p class="documentacion-empty">No se encontraron jugadores con esos filtros.</p>
		<?php else: ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Jugador</th>
						<th>DNI</th>
						<th>Equipo</th>
						<th>DNI doc</th>
						<th>Certificado</th>
						<th>Lista</th>
						<th>Declaracion</th>
						<th>Archivos</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($resultados as $jugador): ?>
						<tr>
							<td><?php echo CHtml::encode($jugador->Nombre); ?></td>
							<td><?php echo CHtml::encode($jugador->DNI); ?></td>
							<td><?php echo $jugador->Equipo ? CHtml::encode($jugador->Equipo->Nombre) : 'Sin equipo'; ?></td>
							<td><?php echo $jugador->fotocopia_dni ? '<span class="documentacion-status documentacion-status-ok">SI</span>' : '<span class="documentacion-status documentacion-status-falta">NO</span>'; ?></td>
							<td><?php echo $jugador->certificado ? '<span class="documentacion-status documentacion-status-ok">SI</span>' : '<span class="documentacion-status documentacion-status-falta">NO</span>'; ?></td>
							<td><?php echo $jugador->firma_lista ? '<span class="documentacion-status documentacion-status-ok">SI</span>' : '<span class="documentacion-status documentacion-status-falta">NO</span>'; ?></td>
							<td><?php echo $jugador->dec_jurada ? '<span class="documentacion-status documentacion-status-ok">SI</span>' : '<span class="documentacion-status documentacion-status-falta">NO</span>'; ?></td>
							<td><?php echo (int)$jugador->getCantidadDocumentosLegajo(); ?></td>
							<td class="documentacion-actions">
								<?php echo CHtml::link('Subir doc', array('jugador/legajo', 'id'=>$jugador->idJugador), array('class'=>'btn btn-primary btn-mini')); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php Yii::app()->clientScript->registerScript('jugadorDocumentacionAutocomplete', "
	(function(){
		var nombre = $('#Jugador_Nombre');
		var idJugador = $('#Jugador_idJugador');
		var form = $('#jugador-documentacion-form');
		var resultados = $('#jugador-resultados');
		var seleccionado = $('#jugador-seleccionado');
		var buscarUrl = '" . CJavaScript::quote($this->createUrl('jugador/jugadorAutocomplete')) . "';
		var timer = null;
		var ultimaBusqueda = '';

		function limpiarResultados(){
			resultados.hide().empty();
		}

		function mostrarEstado(texto){
			resultados.html($('<div/>', {'class':'jugador-estado', text:texto})).show();
		}

		function seleccionarJugador(item){
			idJugador.val(item.id);
			nombre.val(item.value);
			nombre.data('selected-value', item.value);
			seleccionado.text('Seleccionado: ' + item.label).show();
			limpiarResultados();
		}

		function renderResultados(items){
			resultados.empty();
			if(!items || !items.length){
				mostrarEstado('No se encontraron jugadores.');
				return;
			}

			$.each(items, function(_, item){
				$('<button/>', {
					type: 'button',
					'class': 'jugador-opcion',
					text: item.label
				}).on('click', function(){
					seleccionarJugador(item);
				}).appendTo(resultados);
			});
			resultados.show();
		}

		function buscarJugadores(){
			var termino = $.trim(nombre.val());
			if(termino.length < 2){
				limpiarResultados();
				return;
			}

			ultimaBusqueda = termino;
			mostrarEstado('Buscando...');
			$.getJSON(buscarUrl, {term: termino})
				.done(function(items){
					if(ultimaBusqueda === $.trim(nombre.val()))
						renderResultados(items);
				})
				.fail(function(){
					mostrarEstado('No se pudo buscar. Intente nuevamente.');
				});
		}

		nombre.on('input', function(){
			if($(this).val() !== $(this).data('selected-value')){
				idJugador.val('');
				seleccionado.hide().empty();
			}

			clearTimeout(timer);
			timer = setTimeout(buscarJugadores, 250);
		});

		nombre.on('focus', function(){
			if($.trim(nombre.val()).length >= 2 && $.trim(idJugador.val()) === '')
				buscarJugadores();
		});

		form.on('submit', function(event){
			if($.trim(idJugador.val()) === ''){
				event.preventDefault();
				alert('Seleccione un jugador de la lista de resultados.');
				nombre.focus();
				return false;
			}
		});

		$(document).on('click', function(event){
			if(!$(event.target).closest('#Jugador_Nombre, #jugador-resultados').length)
				limpiarResultados();
		});
	})();
"); ?>
