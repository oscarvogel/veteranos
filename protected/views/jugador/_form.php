<?php Yii::app()->clientScript->registerCss('jugadorFormPresentacion', '
    .jugador-form-shell {
        max-width: 860px;
        margin-top: 24px;
    }
    .jugador-form-card {
        background: #fff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15,23,42,0.06);
        padding: 24px;
    }
    .jugador-form-card .note {
        margin: 0 0 18px;
        color: #526070;
        font-size: 13px;
    }
    .jugador-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(240px, 1fr));
        gap: 18px 22px;
    }
    .jugador-form-field {
        margin: 0;
    }
    .jugador-form-field label {
        display: block;
        margin-bottom: 6px;
        color: #1f2937;
        font-weight: bold;
        font-size: 13px;
        text-align: left;
    }
    .jugador-form-field input[type="text"],
    .jugador-form-field select {
        width: 100%;
        max-width: 100%;
        height: 36px;
        box-sizing: border-box;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 7px 10px;
        color: #111827;
        background: #fff;
    }
    .jugador-form-field input:focus,
    .jugador-form-field select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        outline: none;
    }
    .jugador-form-field-wide {
        grid-column: 1 / -1;
    }
    .jugador-form-help {
        display: block;
        margin-top: 5px;
        color: #6b7280;
        font-size: 12px;
    }
    .jugador-checks {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #e5e7eb;
    }
    .jugador-checks-title {
        margin: 0 0 12px;
        color: #1f2937;
        font-size: 14px;
        font-weight: bold;
    }
    .jugador-checks-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 10px 18px;
    }
    .jugador-check {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 28px;
        margin: 0;
        color: #1f2937;
        font-weight: normal;
    }
    .jugador-check input {
        margin: 0;
    }
    .jugador-form-actions {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #e5e7eb;
    }
    .jugador-form-card .errorMessage {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
    }
    .jugador-form-card .errorSummary {
        margin-bottom: 18px;
        border-radius: 6px;
    }
    @media (max-width: 760px) {
        .jugador-form-card {
            padding: 18px;
        }
        .jugador-form-grid,
        .jugador-checks-grid {
            grid-template-columns: 1fr;
        }
    }
'); ?>

<div class="form jugador-form-shell">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'jugador-form',
    'enableAjaxValidation'=>true,
    'htmlOptions'=>array('class'=>'jugador-form-card'),
)); ?>

    <p class="note">Campos con <span class="required">*</span> son requeridos.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="jugador-form-grid">
        <div class="jugador-form-field jugador-form-field-wide">
            <?php echo $form->labelEx($model,'Nombre'); ?>
            <?php echo $form->textField($model,'Nombre',array('maxlength'=>200, 'placeholder'=>'Apellido y nombres')); ?>
            <?php echo $form->error($model,'Nombre'); ?>
        </div>

        <div class="jugador-form-field">
            <?php echo $form->labelEx($model,'fecha_nacimiento'); ?>
            <?php echo $form->textField($model,'fecha_nacimiento',array('maxlength'=>10, 'placeholder'=>'DD/MM/YYYY')); ?>
            <span class="jugador-form-help">Formato: dd/mm/aaaa.</span>
            <?php echo $form->error($model,'fecha_nacimiento'); ?>
        </div>

        <div class="jugador-form-field">
            <?php echo $form->labelEx($model,'DNI'); ?>
            <?php echo $form->textField($model,'DNI',array('maxlength'=>8, 'placeholder'=>'Solo numeros')); ?>
            <?php echo $form->error($model,'DNI'); ?>
        </div>

        <div class="jugador-form-field jugador-form-field-wide">
            <?php echo $form->labelEx($model,'Observacion'); ?>
            <?php echo $form->textField($model,'Observacion',array('maxlength'=>200, 'placeholder'=>'Observaciones internas')); ?>
            <?php echo $form->error($model,'Observacion'); ?>
        </div>

        <div class="jugador-form-field jugador-form-field-wide">
            <?php echo $form->labelEx($model,'idEquipo'); ?>
            <?php echo $form->dropDownList($model,'idEquipo',Equipos::getListEquipo()); ?>
            <?php echo $form->error($model,'idEquipo'); ?>
        </div>
    </div>

    <div class="jugador-checks">
        <p class="jugador-checks-title">Documentacion</p>
        <div class="jugador-checks-grid">
            <label class="jugador-check">
                <?php echo $form->checkBox($model,'certificado'); ?>
                <span>Certificado buena salud</span>
            </label>
            <label class="jugador-check">
                <?php echo $form->checkBox($model,'firma_lista'); ?>
                <span>Lista firmada</span>
            </label>
            <label class="jugador-check">
                <?php echo $form->checkBox($model,'fotocopia_dni'); ?>
                <span>Fotocopia DNI</span>
            </label>
            <label class="jugador-check">
                <?php echo $form->checkBox($model,'dec_jurada'); ?>
                <span>Declaracion jurada</span>
            </label>
        </div>
    </div>

    <div class="jugador-form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label' => $model->isNewRecord ? 'Crear jugador' : 'Guardar cambios',
        )); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
