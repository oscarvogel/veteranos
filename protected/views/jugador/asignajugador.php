<?php
$requiereFechaNacimiento = isset($jugador) && $jugador->idEquipo == 0 && trim((string)$jugador->fecha_nacimiento) === '';
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'jugador-form',
	'type'=>'search',
	'enableAjaxValidation'=>true,
	'htmlOptions'=>array('class'=>'well'),
)); ?>
	<?php echo $form->errorSummary($jugadores); ?>
	<?php echo $form->hiddenField($jugadores,'idJugador',array('size'=>11,'maxlength'=>11)); ?>
		<?php
			echo $form->labelEx($jugadores,'Nombre'); 
            echo $form->textField($jugadores, 'Nombre', array(
                'id'=>'Jugador_Nombre',
                'class'=>'input-large',
                'style'=>'height:20px;',
                'autocomplete'=>'off',
            ));
        ?>
        <div id="jugador-resultados" class="jugador-resultados" style="display:none;"></div>
        <div id="jugador-seleccionado" class="jugador-seleccionado" style="<?php echo $jugadores->idJugador ? '' : 'display:none;'; ?>">
            <?php if($jugadores->idJugador){?>
                Seleccionado: <?php echo CHtml::encode($jugadores->Nombre); ?>
            <?php }?>
        </div>
    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Consultar por nombre')); ?>
        <div class="row">
            <?php if(isset($jugador)){?>
                <?php if($jugador->idEquipo == 0){?>
                    <?php if($requiereFechaNacimiento){?>
                        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'type'=> 'primary', 'label'=>'Asignar', 'htmlOptions'=>array('class' => 'button primary', 'id'=>'btnAbrirFechaNacimiento'))); ?>
                    <?php }else{?>
                        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=> 'primary', 'label'=>'Asignar', 'htmlOptions'=>array('class' => 'button primary', 'name'=>'btnAsignar'))); ?>
                    <?php }?>
                <?php }?>
                <?php if($jugador->idEquipo == $equipo->idEquipo){?>
                    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Borrar Asignacion','htmlOptions'=>array('class' => 'button danger', 'name'=>'btnBorrar'))); ?>
                <?php }?>
            <?php }?>
        </div>
    </div>
	<?php $this->endWidget(); ?>
	
</div>
<?php 
if(isset($jugador)){?>
    <div class="row">
        Id Jugador <?php echo $jugador->idJugador; ?>
    </div>
    <div class="row">
        Nombre <?php echo $jugador->Nombre; ?>
    </div>
    <div class="row">
        Clase <?php echo $jugador->Clase; ?>
    </div>
    <div class="row">
        DNI <?php echo $jugador->DNI; ?>
    </div>
    <div class="row">
        Observacion <?php echo $jugador->Observacion; ?>
    </div>
    <div class="row">
        <?php $equipoActual = $jugador->Equipo; ?>
        Equipo actual <span class="button danger"><?php echo $equipoActual ? $equipoActual->Nombre : 'Sin equipo'; ?></span>
    </div>

    <div class="row">
        Asignar a <?php echo $equipo->Nombre; ?>
    </div>
<?php }
?>

<?php Yii::app()->clientScript->registerCss('jugadorAsignacionBuscadorCss', '
    #jugador-resultados.jugador-resultados {
        width: 360px;
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
    #jugador-resultados .jugador-estado {
        padding: 8px 10px;
        color: #64748b;
        font-size: 13px;
    }
    #jugador-seleccionado.jugador-seleccionado {
        width: 360px;
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
'); ?>

<?php Yii::app()->clientScript->registerScript('jugadorAsignacionAutocomplete', "
    (function(){
        var nombre = $('#Jugador_Nombre');
        var idJugador = $('#Jugador_idJugador');
        var form = $('#jugador-form');
        var resultados = $('#jugador-resultados');
        var seleccionado = $('#jugador-seleccionado');
        var buscarUrl = '" . CJavaScript::quote($this->createUrl('jugador/jugadorAutocomplete')) . "';
        var timer = null;
        var ultimaBusqueda = '';

        if(idJugador.val() !== '')
            nombre.data('selected-value', nombre.val());

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
            if($.trim(nombre.val()) !== '' && $.trim(idJugador.val()) === ''){
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

<?php if($requiereFechaNacimiento){?>
    <?php Yii::app()->clientScript->registerCss('fechaNacimientoModalCss', '
        body.fecha-nacimiento-modal-open {
            overflow: hidden;
        }
        #fechaNacimientoBackdrop {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 30000;
            background: #000;
            opacity: 0.55;
            filter: alpha(opacity=55);
        }
        #fechaNacimientoModal.fecha-nacimiento-modal {
            position: fixed;
            top: 50%;
            right: auto;
            bottom: auto;
            left: 50%;
            z-index: 30010;
            width: 520px;
            max-width: 92%;
            height: auto;
            min-height: 0;
            margin: 0;
            transform: translate(-50%, -50%);
            background: #fff;
            border: 1px solid #999;
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.45);
            color: #333;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd;
            background: #f7f7f7;
            border-radius: 6px 6px 0 0;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-header h3 {
            margin: 0;
            font-size: 22px;
            line-height: 1.25;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-body {
            padding: 18px;
            max-height: 60vh;
            overflow-y: auto;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-body p {
            margin: 0 0 10px;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .control-group {
            margin: 14px 0 0;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .control-label {
            display: block;
            float: none;
            width: auto;
            margin-bottom: 6px;
            text-align: left;
            font-weight: bold;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .controls {
            margin-left: 0;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal input[type="text"] {
            width: 180px;
            height: 28px;
            box-sizing: border-box;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .help-block {
            display: block;
            margin-top: 6px;
            color: #666;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .errorSummary {
            margin: 12px 0;
            padding: 10px 12px;
            border: 1px solid #d6a4a4;
            background: #f9eeee;
            color: #7b1f1f;
            border-radius: 4px;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-footer {
            padding: 12px 18px;
            text-align: right;
            border-top: 1px solid #ddd;
            background: #f7f7f7;
            border-radius: 0 0 6px 6px;
        }
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-footer .btn,
        #fechaNacimientoModal.fecha-nacimiento-modal .modal-footer button {
            margin-left: 8px;
        }
    '); ?>
    <div id="fechaNacimientoModal" class="modal hide fade fecha-nacimiento-modal" tabindex="-1" role="dialog" aria-labelledby="fechaNacimientoModalLabel" aria-hidden="true" data-backdrop="static">
        <?php echo CHtml::beginForm($this->createUrl('jugador/asignaequipo'), 'post', array('id'=>'fecha-nacimiento-form', 'class'=>'form-horizontal')); ?>
            <?php echo CHtml::hiddenField('Jugador[idJugador]', $jugador->idJugador, array('id'=>'Jugador_idJugador_modal')); ?>
            <div class="modal-header">
                <h3 id="fechaNacimientoModalLabel">Fecha de nacimiento requerida</h3>
            </div>
            <div class="modal-body">
                <p>Debe cargar la fecha de nacimiento para asignar este jugador.</p>
                <p><strong>Formato: dd/mm/aaaa</strong></p>
                <?php echo CHtml::errorSummary($jugador); ?>
                <div class="control-group<?php echo $jugador->hasErrors('fecha_nacimiento') ? ' error' : ''; ?>">
                    <?php echo CHtml::label('Fecha de nacimiento', 'Jugador_fecha_nacimiento_modal', array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo CHtml::textField('Jugador[fecha_nacimiento]', isset($_POST['Jugador']['fecha_nacimiento']) ? $_POST['Jugador']['fecha_nacimiento'] : '', array('id'=>'Jugador_fecha_nacimiento_modal', 'placeholder'=>'dd/mm/aaaa', 'maxlength'=>10)); ?>
                        <span class="help-block">Ingrese una fecha valida, por ejemplo 25/06/1978.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Guardar y asignar', 'htmlOptions'=>array('name'=>'btnAsignar'))); ?>
                <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'label'=>'Cancelar', 'htmlOptions'=>array('id'=>'btnCerrarFechaNacimiento'))); ?>
            </div>
        <?php echo CHtml::endForm(); ?>
    </div>
<?php }?>

<?php if($requiereFechaNacimiento){?>
    <?php Yii::app()->clientScript->registerScript('fechaNacimientoModalManual', "
        (function(){
            function abrirFechaNacimientoModal(){
                var modal = document.getElementById('fechaNacimientoModal');
                if(!modal)
                    return;

                modal.className = modal.className.replace('hide', '');
                if(modal.className.indexOf('in') === -1)
                    modal.className += ' in';
                modal.style.display = 'block';
                modal.setAttribute('aria-hidden', 'false');
                document.body.className += document.body.className.indexOf('fecha-nacimiento-modal-open') === -1 ? ' fecha-nacimiento-modal-open' : '';

                if(!document.getElementById('fechaNacimientoBackdrop')){
                    var backdrop = document.createElement('div');
                    backdrop.id = 'fechaNacimientoBackdrop';
                    backdrop.className = 'modal-backdrop fade in';
                    document.body.appendChild(backdrop);
                }
            }

            function cerrarFechaNacimientoModal(){
                var modal = document.getElementById('fechaNacimientoModal');
                if(modal){
                    modal.className = modal.className.replace(' in', '');
                    if(modal.className.indexOf('hide') === -1)
                        modal.className += ' hide';
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }

                var backdrop = document.getElementById('fechaNacimientoBackdrop');
                if(backdrop && backdrop.parentNode)
                    backdrop.parentNode.removeChild(backdrop);
                document.body.className = document.body.className.replace(' fecha-nacimiento-modal-open', '').replace('fecha-nacimiento-modal-open', '');
            }

            var abrir = document.getElementById('btnAbrirFechaNacimiento');
            if(abrir)
                abrir.onclick = function(event){
                    if(event && event.preventDefault)
                        event.preventDefault();
                    abrirFechaNacimientoModal();
                    return false;
                };

            var cerrar = document.getElementById('btnCerrarFechaNacimiento');
            if(cerrar)
                cerrar.onclick = function(event){
                    if(event && event.preventDefault)
                        event.preventDefault();
                    cerrarFechaNacimientoModal();
                    return false;
                };

            " . (!empty($mostrarModalFechaNacimiento) ? "abrirFechaNacimientoModal();" : "") . "
        })();
    "); ?>
<?php }?>
