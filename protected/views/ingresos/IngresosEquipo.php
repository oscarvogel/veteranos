<h1>Ingresos por equipo</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="row">
	<?php echo $form->dropDownListRow($EquiposTorneo,'idTorneo',Torneo::getListTorneo(),
		 				array(
                            'ajax'=>array(
                             	'type'=>'POST',
                              	'url'=>CController::createUrl('Equipos/SelectEquipos'),
                              	'update'=>'#'.CHtml::activeId($EquiposTorneo,'idEquipo'),
                              	'beforeSend' => 'function(){
                               		$("#' . CHtml::activeId($EquiposTorneo,'idEquipo') . '").find("option").remove();
                               	}',  
                            ),'prompt'=>'Seleccione'
                        )
	); ?>
</div>

<div class="row">
	<?php echo $form->dropDownListRow($EquiposTorneo, 'idEquipo', Equipos::getListEquipo());?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar')); ?>
</div>
<?php $this->endWidget();
?>


<?php if(isset($Ingresos)){?>
	<table class="table">
		<thead>
			<th>Concepto</th>
			<th>Fecha</th>
			<th>Nº Fecha</th>
			<th>Monto</th>
		</thead>
	<?php foreach ($Ingresos as $Ingreso) {?>
		<tr>
			<td><?php echo $Ingreso->Conceptos->Nombre;?></td>
			<td><?php echo $Ingreso->Fecha;?></td>
			<td><?php echo $Ingreso->NFecha;?></td>
			<td><?php echo $Ingreso->Monto;?></td>
		</tr>
	<?php }
}?>
</table>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Agregar Ingreso',
    'type'=>'null', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'large', // null, 'large', 'small' or 'mini'
	'url'=>Yii::app()->createUrl('ingresos/create'),
)); ?>