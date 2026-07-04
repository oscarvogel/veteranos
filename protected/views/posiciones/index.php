<?php
/* @var $this PosicionesController */


$this->breadcrumbs=array(
	'Posiciones',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'posicionesForm',
    'type'=>'inline',
    'htmlOptions'=>array('class'=>'well'),
));?>
<div class="row">
	<?php echo $form->dropDownListRow($model,'idTorneo',Torneo::getListTorneo('I'),
                                     array('class'=>'form-control')); ?>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consultar', 'htmlOptions'=>array('class' => 'button primary'))); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('class' => 'button success','name'=>'btnExcel'))); ?>
</div>
<?php $this->endWidget();
?>
<?php if(isset($posiciones)){?>
	<h2>Posiciones Finales</h2>
	<table class="table">
		<thead>
			<th>Equipo</th>
			<th>Posicion</th>
		</thead>
		<?php foreach ($posiciones as $posicion) {?>
			<tr>
				<td><?php echo $posicion->Equipo->Nombre;?></td>
				<td><?php echo $posicion->Posicion;?></td>
			</tr>
		<?php }?>
	</table>
<?php }?>
<h3>Tabla de Posiciones</h3>
<table class="table">
	<thead>
		<tr>
			<th>Posicion</th>
			<th>Nombre</th>
			<th>PJ</th>
			<th>PG</th>
			<th>PE</th>
			<th>PP</th>
			<th>GF</th>
			<th>GC</th>
			<th>Dif.</th>
			<th>Puntos</th>
		</tr>
	</thead>
<?php
	//print_r($datos);
	if(!empty($datos)){
		$i = 1;
		foreach ($datos as $dato) {?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $dato['Nombre'];?></td>
				<td><?php echo $dato['Partidos'];?></td>
				<td><?php echo $dato['Ganados'];?></td>
				<td><?php echo $dato['Empatados'];?></td>
				<td><?php echo $dato['Perdidos'];?></td>
				<td><?php echo $dato['GolFavor'];?></td>
				<td><?php echo $dato['GolContra'];?></td>
				<td><?php echo $dato['Diferencia'];?></td>
				<td><?php echo $dato['Puntos'];?></td>
			</tr>
		<?php
		$i++; 
		}
	}
?>
</table>