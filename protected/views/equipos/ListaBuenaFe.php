<?php
Yii::app()->clientScript->registerCss('listaBuenaFeDocumentacionCss', '
	.container.main-content {
		width: calc(100% - 32px);
		max-width: none;
		padding-left: 16px;
		padding-right: 16px;
	}
	.lista-buena-fe-page {
		width: 100%;
	}
	.lista-buena-fe-page .well {
		max-width: none;
	}
	.lista-buena-fe-table-wrap {
		width: 100%;
		overflow-x: auto;
	}
	.lista-buena-fe-table {
		width: 100%;
		min-width: 1180px;
	}
	.lista-buena-fe-table th,
	.lista-buena-fe-table td {
		vertical-align: top;
	}
	.lista-buena-fe-cards {
		display: none;
	}
	.lista-buena-fe-card {
		border: 1px solid #dbe3ee;
		border-radius: 6px;
		background: #fff;
	}
	.lista-card-head {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 10px;
	}
	.lista-card-number {
		flex: 0 0 auto;
		min-width: 34px;
		padding: 4px 8px;
		border-radius: 4px;
		background: #eef2ff;
		color: #3730a3;
		font-weight: bold;
		text-align: center;
	}
	.lista-card-title {
		flex: 1 1 auto;
		min-width: 0;
	}
	.lista-card-name {
		margin: 0;
		font-size: 16px;
		font-weight: bold;
		line-height: 1.25;
		color: #0f172a;
	}
	.lista-card-team {
		margin: 3px 0 0;
		color: #526070;
		font-size: 12px;
	}
	.lista-card-meta,
	.lista-card-doc-state {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 8px;
		margin-top: 12px;
	}
	.lista-card-field {
		min-width: 0;
	}
	.lista-card-label {
		display: block;
		margin-bottom: 2px;
		color: #526070;
		font-size: 11px;
		font-weight: bold;
	}
	.lista-card-value {
		color: #0f172a;
		font-weight: bold;
		word-break: break-word;
	}
	.lista-card-wide {
		grid-column: 1 / -1;
	}
	.lista-doc-pill {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 8px;
		padding: 6px 8px;
		border-radius: 4px;
		background: #f8fafc;
		font-size: 12px;
	}
	.lista-doc-pill span {
		font-weight: bold;
	}
	.lista-doc-pill-ok span {
		color: #216e39;
	}
	.lista-doc-pill-miss span {
		color: #991b1b;
	}
	.lista-card-actions {
		margin-top: 12px;
	}
	.lista-card-missing {
		margin-top: 12px;
	}
	.lista-card-photo-form {
		margin: 12px 0 0;
	}
	.lista-card-photo-btn.btn {
		width: 100%;
		box-sizing: border-box;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.lista-photo-file {
		position: absolute;
		left: -9999px;
		width: 1px;
		height: 1px;
		opacity: 0;
	}
	.lista-card-photo-message {
		margin-top: 6px;
		min-height: 16px;
		font-size: 12px;
		font-weight: bold;
	}
	.lista-card-photo-message.ok {
		color: #216e39;
	}
	.lista-card-photo-message.error {
		color: #991b1b;
	}
	.lista-card-actions .btn {
		width: 100%;
		box-sizing: border-box;
		padding-top: 9px;
		padding-bottom: 9px;
	}
	.lista-doc-ok {
		display: inline-block;
		padding: 2px 7px;
		border-radius: 10px;
		background: #e7f6ec;
		color: #216e39;
		font-size: 11px;
		font-weight: bold;
	}
	.lista-doc-faltantes {
		max-width: 260px;
		line-height: 1.4;
	}
	.lista-doc-tag {
		display: inline-block;
		margin: 1px 2px 1px 0;
		padding: 2px 6px;
		border-radius: 10px;
		background: #fff4d6;
		color: #7a4b00;
		font-size: 11px;
		font-weight: bold;
		white-space: nowrap;
	}
	.lista-doc-actions {
		white-space: nowrap;
	}
	@media (max-width: 760px) {
		.container.main-content {
			width: 100%;
			padding-left: 10px;
			padding-right: 10px;
		}
		.lista-buena-fe-page .well {
			padding: 14px;
		}
		.lista-buena-fe-table-wrap {
			display: none;
		}
		.lista-buena-fe-cards {
			display: block;
		}
		.lista-buena-fe-card {
			margin-bottom: 10px;
			padding: 12px;
		}
		.lista-doc-faltantes {
			max-width: none;
		}
		.lista-card-meta,
		.lista-card-doc-state {
			grid-template-columns: 1fr 1fr;
		}
	}
	@media (max-width: 420px) {
		.lista-card-meta,
		.lista-card-doc-state {
			grid-template-columns: 1fr;
		}
	}
');

?>
<div class="lista-buena-fe-page">
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>
<fieldset>
	<legend>Seleccione Torneo y Equipo</legend>
	<div class="row">
		<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo('I'),
		 				array(
                            'ajax'=>array(
                             	'type'=>'POST',
                              	'url'=>CController::createUrl('Equipos/SelectEquipos'),
                              	'update'=>'#'.CHtml::activeId($model,'idEquipo'),
                              	'beforeSend' => 'function(){
                               		$("#' . CHtml::activeId($model,'idEquipo') . '").find("option").remove();
                               	}',  
                            ),'prompt'=>'Seleccione'
							)
	); ?>
	</div>
	<div class="row">
		<?php echo $form->dropDownListRow($model,'idEquipo'); ?>
	</div>
	<div class="form-actions">
	    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar')); ?>
	    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'warning', 'label'=>'Lista', 'htmlOptions'=>array('name'=>'btnLista', 'id'=>'btnListaBuenaFe'))); ?>
	    <?php echo CHtml::link(
            '<i class="icon-download-alt icon-white"></i> Reverso',
            Yii::app()->baseUrl . '/media/arbitros-2026.pdf',
            array(
                'class'=>'btn btn-info',
                'id'=>'btnReversoListaBuenaFe',
                'download'=>'Arbitros 2026.pdf',
                'title'=>'Descargar reverso',
            )
        ); ?>
	    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('name'=>'btnExcel'))); ?>
	</div>
</fieldset>
<?php $this->endWidget();?>

<?php

if(isset($jugadores)){?>
	<div class="notice marker-on-top fg-white">
    	<p>Delegado: <?php echo $equipo->Delegado;?></p>
    	<p>Telefono: <?php echo $equipo->Telefono;?></p>
	</div>
	<?php
	$filasJugadores = array();
	$i = 1;
	foreach ($jugadores as $jugador) {
		$faltantes = $jugador->getFaltantesDocumentacionLegajo(true);
		$primerTipoFaltante = $jugador->getPrimerTipoFaltanteDocumentacionLegajo(true);
		$legajoUrl = array('/jugador/legajo', 'id'=>$jugador->idJugador);
		if($primerTipoFaltante !== '')
			$legajoUrl['tipo'] = $primerTipoFaltante;

		$filasJugadores[] = array(
			'numero'=>$i++,
			'jugador'=>$jugador,
			'faltantes'=>$faltantes,
			'legajoUrl'=>$legajoUrl,
		);
	}
	?>
	<div class="lista-buena-fe-table-wrap">
	<table class="table lista-buena-fe-table">
		<thead>
			<th>Nº</th>
			<th>Nombre</th>
			<th>DNI</th>
			<th>Clase</th>
			<th>Observaciones</th>
			<th>Certificado</th>
			<th>Firmo lista</th>
			<th>Fotocopia DNI</th>
			<th>Declaracion Jurada</th>
			<th>Faltantes</th>
			<th>Documentacion</th>
		</thead>
	<?php
	foreach ($filasJugadores as $fila) {
		$jugador = $fila['jugador'];
		$faltantes = $fila['faltantes'];
		$legajoUrl = $fila['legajoUrl'];
	?>

		<tr>
			<td><?php echo $fila['numero'];?></td>
			<td><?php echo CHtml::encode($jugador->Nombre);?></td>
			<td><?php echo CHtml::encode($jugador->DNI);?></td>
			<td><?php echo CHtml::encode($jugador->Clase);?></td>
			<td><?php echo CHtml::encode($jugador->Observacion);?></td>
			<td><?php echo $jugador->certificado ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->firma_lista ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->fotocopia_dni ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->dec_jurada ? 'SI' : 'NO';?></td>
			<td class="lista-doc-faltantes">
				<?php if(empty($faltantes)): ?>
					<span class="lista-doc-ok">Completo</span>
				<?php else: ?>
					<?php foreach($faltantes as $faltante): ?>
						<span class="lista-doc-tag"><?php echo CHtml::encode($faltante); ?></span>
					<?php endforeach; ?>
				<?php endif; ?>
			</td>
			<td class="lista-doc-actions">
				<?php echo CHtml::link(empty($faltantes) ? 'Ver legajo' : 'Cargar faltante', $legajoUrl, array('class'=>'btn btn-primary btn-mini')); ?>
			</td>
		</tr>
		
	<?php }?>
	</table>
	</div>
	<div class="lista-buena-fe-cards">
		<?php foreach ($filasJugadores as $fila): ?>
			<?php
			$jugador = $fila['jugador'];
			$faltantes = $fila['faltantes'];
			$legajoUrl = $fila['legajoUrl'];
			?>
			<div class="lista-buena-fe-card">
				<div class="lista-card-head">
					<div class="lista-card-number"><?php echo $fila['numero']; ?></div>
					<div class="lista-card-title">
						<p class="lista-card-name"><?php echo CHtml::encode($jugador->Nombre); ?></p>
						<p class="lista-card-team"><?php echo $jugador->Equipo ? CHtml::encode($jugador->Equipo->Nombre) : 'Sin equipo'; ?></p>
					</div>
				</div>
				<div class="lista-card-meta">
					<div class="lista-card-field">
						<span class="lista-card-label">DNI</span>
						<span class="lista-card-value"><?php echo CHtml::encode($jugador->DNI); ?></span>
					</div>
					<div class="lista-card-field">
						<span class="lista-card-label">Clase</span>
						<span class="lista-card-value"><?php echo CHtml::encode($jugador->Clase); ?></span>
					</div>
					<?php if(trim((string)$jugador->Observacion) !== ''): ?>
						<div class="lista-card-field lista-card-wide">
							<span class="lista-card-label">Observaciones</span>
							<span class="lista-card-value"><?php echo CHtml::encode($jugador->Observacion); ?></span>
						</div>
					<?php endif; ?>
				</div>
				<div class="lista-card-doc-state">
					<div class="lista-doc-pill <?php echo $jugador->certificado ? 'lista-doc-pill-ok' : 'lista-doc-pill-miss'; ?>">Certificado <span><?php echo $jugador->certificado ? 'SI' : 'NO'; ?></span></div>
					<div class="lista-doc-pill <?php echo $jugador->firma_lista ? 'lista-doc-pill-ok' : 'lista-doc-pill-miss'; ?>">Lista <span><?php echo $jugador->firma_lista ? 'SI' : 'NO'; ?></span></div>
					<div class="lista-doc-pill <?php echo $jugador->fotocopia_dni ? 'lista-doc-pill-ok' : 'lista-doc-pill-miss'; ?>">DNI <span><?php echo $jugador->fotocopia_dni ? 'SI' : 'NO'; ?></span></div>
					<div class="lista-doc-pill <?php echo $jugador->dec_jurada ? 'lista-doc-pill-ok' : 'lista-doc-pill-miss'; ?>">Jurada <span><?php echo $jugador->dec_jurada ? 'SI' : 'NO'; ?></span></div>
				</div>
				<div class="lista-doc-faltantes lista-card-missing">
					<?php if(empty($faltantes)): ?>
						<span class="lista-doc-ok">Completo</span>
					<?php else: ?>
						<?php foreach($faltantes as $faltante): ?>
							<span class="lista-doc-tag"><?php echo CHtml::encode($faltante); ?></span>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<?php echo CHtml::beginForm(array('/jugador/subirDocumento', 'id'=>$jugador->idJugador), 'post', array('class'=>'lista-card-photo-form', 'enctype'=>'multipart/form-data')); ?>
					<?php echo CHtml::hiddenField('fotoRapida', '1', array('id'=>'fotoRapidaFlag_' . (int)$jugador->idJugador)); ?>
					<?php echo CHtml::hiddenField('JugadorDocumento[tipo]', JugadorDocumento::TIPO_FOTO, array('id'=>'fotoRapidaTipo_' . (int)$jugador->idJugador)); ?>
					<?php echo CHtml::hiddenField('JugadorDocumento[titulo]', 'Foto jugador', array('id'=>'fotoRapidaTitulo_' . (int)$jugador->idJugador)); ?>
					<?php echo CHtml::fileField('archivo', '', array(
						'id'=>'fotoRapida_' . (int)$jugador->idJugador,
						'class'=>'lista-photo-file',
						'accept'=>'image/*',
						'capture'=>'environment',
					)); ?>
					<button type="button" class="btn btn-success lista-card-photo-btn">Sacar foto y enviar</button>
					<div class="lista-card-photo-message" aria-live="polite"></div>
				<?php echo CHtml::endForm(); ?>
				<div class="lista-card-actions">
					<?php echo CHtml::link(empty($faltantes) ? 'Ver legajo' : 'Cargar faltante', $legajoUrl, array('class'=>'btn btn-primary')); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php 
}
if(isset($data)){?>
<b>Lista de buena fe:</b>
	<?php echo CHtml::link(CHtml::encode($data->Equipos->Nombre),
                Yii::app()->baseUrl . '/' . CHtml::encode($data->lista)); ?>
	<br />
<?php }
?>
</div>

<?php
Yii::app()->clientScript->registerScript('listaBuenaFeFotoRapida', '
	var forms = document.getElementsByClassName("lista-card-photo-form");
	for(var i = 0; i < forms.length; i++) {
		(function(form) {
			var file = form.getElementsByClassName("lista-photo-file")[0];
			var button = form.getElementsByClassName("lista-card-photo-btn")[0];
			var message = form.getElementsByClassName("lista-card-photo-message")[0];
			if(!file || !button || !message)
				return;

			function setMessage(text, className) {
				message.className = "lista-card-photo-message" + (className ? " " + className : "");
				message.innerHTML = text;
			}

			button.onclick = function() {
				file.click();
			};

			file.onchange = function() {
				if(!file.value)
					return;

				if(!window.FormData || !window.fetch) {
					form.submit();
					return;
				}

				button.disabled = true;
				button.innerHTML = "Enviando foto...";
				setMessage("", "");

				fetch(form.action, {
					method: "POST",
					body: new FormData(form),
					credentials: "same-origin"
				}).then(function(response) {
					return response.json();
				}).then(function(data) {
					if(data && data.ok) {
						button.innerHTML = "Foto enviada";
						button.disabled = false;
						file.value = "";
						setMessage("Lista.", "ok");
						return;
					}

					button.innerHTML = "Sacar foto y enviar";
					button.disabled = false;
					file.value = "";
					setMessage(data && data.message ? data.message : "No se pudo subir la foto.", "error");
				}).catch(function() {
					button.innerHTML = "Sacar foto y enviar";
					button.disabled = false;
					file.value = "";
					setMessage("No se pudo subir la foto.", "error");
				});
			};
		})(forms[i]);
	}
');
?>
