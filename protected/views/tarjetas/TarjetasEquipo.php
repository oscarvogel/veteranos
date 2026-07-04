<?php
/* @var $this TarjetasController */
/* @var $model FormularioTarjetasModel */ // Puedes usar un modelo vacío o CActiveRecord si prefieres
?>

<?php
// Inicia el formulario
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'posicionesForm',
    'type' => 'horizontal',
    'htmlOptions' => array('class' => 'well'),
    'enableAjaxValidation' => false,
    'action' => '#', // No se hace POST normal
));
?>

<fieldset>
    <legend>Seleccione Equipo</legend>

    <div class="row">
        <?php echo $form->dropDownListRow($model, 'idTorneo', Torneo::getListTorneo(), array(
            'ajax' => array(
                'type' => 'POST',
                'url' => Yii::app()->createUrl('equipos/selectEquipos'), // Asegúrate que esta ruta exista
                'update' => '#' . CHtml::activeId($model, 'idEquipo'),
                'beforeSend' => "function(){
                    $('#" . CHtml::activeId($model, 'idEquipo') . "').find('option').remove();
                }",
            ),
            'prompt' => 'Seleccione un Torneo',
        )); ?>
    </div>

    <div class="row">
        <?php echo $form->dropDownListRow($model, 'idEquipo', Equipos::getListEquipo(), array(
            'prompt' => 'Seleccione un Equipo',
        )); ?>
    </div>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type' => 'primary',
            'label' => 'Consultar'
        )); ?>
    </div>
</fieldset>

<?php $this->endWidget(); ?>


<?php
// Script para manejar el submit del formulario y redirigir con parámetros GET
Yii::app()->clientScript->registerScript('redirect-form', '
    $("#posicionesForm").on("submit", function(e) {
        e.preventDefault();

        var idTorneo = $("#' . CHtml::activeId($model, 'idTorneo') . '").val();
        var idEquipo = $("#' . CHtml::activeId($model, 'idEquipo') . '").val();

        if (idTorneo && idEquipo) {
            var url = "' . Yii::app()->createUrl('tarjetas/TarjetasAmarillasEquipoTorneo') . '&idTorneo=" + idTorneo + "&idEquipo=" + idEquipo;
            window.location.href = url;
        } else {
            alert("Por favor seleccione un Torneo y un Equipo.");
        }
    });
', CClientScript::POS_END);
?>