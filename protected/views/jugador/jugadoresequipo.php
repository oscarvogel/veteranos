<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'jugador-form',
	'type'=>'search',
	'enableAjaxValidation'=>true,
	'htmlOptions'=>array('class'=>'well'),
)); ?>
	<?php echo $form->errorSummary($equipos); ?>
	<?php echo $form->hiddenField($equipos,'idEquipo',array('size'=>11,'maxlength'=>11)); ?>
		<?php
			echo $form->labelEx($equipos,'Nombre Equipo'); 
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
	            'model'=>$equipos,
	            'attribute'=>'Nombre',
	            'name'=>'Nombre',
	            'source'=>$this->createUrl('equipos/equiposAutocomplete'),  // Controller/Action path for action we created in step 4.
	            // additional javascript options for the autocomplete plugin
	            'options'=>array(
					'showAnim'=>'fold',
	                'minLength'=>'2',
	                'select'=>"js:function(event, ui) {
	                	 $('#Equipos_idEquipo').val(ui.item.id);
	                }"
	            ),
	            'htmlOptions'=>array(
	                'style'=>'height:20px;',
	                'class'=>'input-large', 
	                'prepend'=>'<i class="icon-search"></i>'
	            ),        
        ));?>
    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=> 'primary', 'label'=>'Consultar jugadores', 'htmlOptions'=>array('class' => 'button primary', 'name'=>'btnConsultar'))); ?>
    </div>
	<?php $this->endWidget(); ?>
	
</div>

<?php
if(isset($jugadores)){?>
    <?php $i=1; ?>
	<table class="table">
	<thead>
        <th>Orden</th>
		<th>Nombre</th>
		<th>DNI</th>
		<th>Clase</th>
		<th>Certificado</th>
		<th>Firma Lista</th>
		<th>Declarcion Jurada</th>
		<th>Fotocopia DNI</th>
		<th>Accion</th>
	</thead>
    <?php foreach ($jugadores as $jugador) {?>
    <tr>
        <td><?php echo $i;$i++?></td>
        <td><?php echo $jugador->Nombre;?></td>
        <td><?php echo $jugador->DNI;?></td>
        <td><?php echo $jugador->Clase;?></td>
		<td><?php echo $jugador->certificado ? 'SI' : 'NO';?></td>
		<td><?php echo $jugador->firma_lista ? 'SI' : 'NO';?></td>
		<td><?php echo $jugador->fotocopia_dni ? 'SI' : 'NO';?></td>
		<td><?php echo $jugador->dec_jurada ? 'SI' : 'NO';?></td>
		<td>
            <a href="<?php echo Yii::app()->createUrl('jugador/update/', array('id' => $jugador->idJugador));?>" class="btn btn-info" target="_blank">Editar</a>
            <a href="<?php echo Yii::app()->createUrl('jugador/legajo', array('id' => $jugador->idJugador));?>" class="btn btn-primary" target="_blank">Legajo</a>
        </td>
    </tr>
    <?php }?>
    </table>
<?php }?>
