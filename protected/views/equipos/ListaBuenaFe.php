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
	<table class="table">
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
		</thead>
	<?php
	$i = 1;
	foreach ($jugadores as $jugador) {?>

		<tr>
			<td><?php echo $i++;?></td>
			<td><?php echo CHtml::encode($jugador->Nombre);?></td>
			<td><?php echo CHtml::encode($jugador->DNI);?></td>
			<td><?php echo CHtml::encode($jugador->Clase);?></td>
			<td><?php echo CHtml::encode($jugador->Observacion);?></td>
			<td><?php echo $jugador->certificado ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->firma_lista ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->fotocopia_dni ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->dec_jurada ? 'SI' : 'NO';?></td>
		</tr>
		
	<?php }?>
	</table>
<?php 
}
if(isset($data)){?>
<b>Lista de buena fe:</b>
	<?php echo CHtml::link(CHtml::encode($data->Equipos->Nombre),
                Yii::app()->baseUrl . '/' . CHtml::encode($data->lista)); ?>
	<br />
<?php }
?>
