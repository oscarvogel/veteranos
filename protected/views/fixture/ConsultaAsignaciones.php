
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'consultaFixtureForm',
    'type'=>'horizontal',
    'htmlOptions'=>array('class'=>'well'),
));?>

<div class="row">
	<?php $this->widget('ext.metro.fieldDateRow',array(
				'model'=>$fixture,
				'valor'=>'Fecha',
			)); 
		?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Consulta Fecha','htmlOptions'=>array('class' => 'button primary'))); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'success', 'label'=>'Enviar a Excel', 'htmlOptions'=>array('class' => 'button success', 'name'=>'btnExcel'))); ?>
</div>
<?php $this->endWidget();?>


<?php if(isset($partidos)){?>
	
	<table class="table table-bordered">
		<thead>
			<th>Nº Fecha</th>
			<th>Local</th>
			<th>Visitante</th>
			<th>Cancha</th>
			<th>Arbitro</th>
			<th>Linea 1</th>
			<th>Linea 2</th>
			<th>Hora</th>
		</thead>
		<?php 
			$fecha = 0;
			foreach ($partidos as $partido) {?>
			<tr class="<?php echo ($partido->Visitante==0) ? 'success' : '' ?>">
				<td><?php echo ($partido->NFecha == $fecha) ? '' : $partido->NFecha; $fecha = $partido->NFecha;?></td>
				<td><?php echo $partido->local->Nombre;?></td>
				<td><?php echo $partido->visitante->Nombre;?></td>
				<td><?php echo $partido->Cancha->Nombre;?></td>
				<td><?php echo $partido->Arbitro->Nombre;?></td>
				<td><?php echo $partido->Linea1->Nombre;?></td>
				<td><?php echo $partido->Linea2->Nombre;?></td>
				<td><?php echo $partido->Hora;?></td>
			</tr>
		<?php }?>
	</table>
<?php 
}?>

