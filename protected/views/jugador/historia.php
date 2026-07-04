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
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
	            'model'=>$jugadores,
	            'attribute'=>'Nombre',
	            'name'=>'Nombre',
	            'source'=>$this->createUrl('jugador/jugadorAutocomplete'),  // Controller/Action path for action we created in step 4.
	            // additional javascript options for the autocomplete plugin
	            'options'=>array(
					'showAnim'=>'fold',
	                'minLength'=>'2',
	                'select'=>"js:function(event, ui) {
	                	 $('#Jugador_idJugador').val(ui.item.id);
	                }"
	            ),
	            'htmlOptions'=>array(
	                'style'=>'height:20px;',
	                'class'=>'input-large', 
	                'prepend'=>'<i class="icon-search"></i>'
	            ),        
        ));?>
	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label' => 'Consultar')); ?>
	<?php $this->endWidget(); ?>
	
</div>

<?php
if(isset($jugador)){
	Yii::app()->user->setFlash('success', '<h4><strong>Equipo Actual: </strong>' . $jugador->Equipo->Nombre .'</h4>');
	$this->widget('bootstrap.widgets.TbAlert', array(
	    'block'=>true, // display a larger alert block?
	    'fade'=>true, // use transitions?
	    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
	    'alerts'=>array( // configurations per alert type
	        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
	    ),
	));
}
if(isset($equipojug)){?>
	<table class="table">
	<thead>
		<th>Torneo</th>
		<th>Equipo</th>
	</thead>
	<?php //print_r($equipojug);?>
	<?php foreach ($equipojug as $ej) {?>
		<tr class="alert">
			<td><?php echo $ej->Torneo->Nombre;?></td>
			<td><?php echo $ej->Equipo->Nombre;?></td>
		</tr>
<?php }	
}
if(isset($amarillas)){?>
	<table class="table">
	<thead>
		<th>Torneo</th>
		<th>Fecha</th>
		<th>Contra Equipo</th>
		<th></th>
	</thead>
	<?php foreach ($amarillas as $tarjeta) {?>
		<tr class="alert alert-warning">
			<td><?php echo $tarjeta->Fixture->Torneo->Nombre; ?></td>
			<td><?php echo $tarjeta->Fixture->NFecha; ?></td>
			<td>
			<?php 
			if($tarjeta->Fixture->Torneo->Estado == 'F'){
				if($tarjeta->idEquipo == $tarjeta->Fixture->Local){
					echo $tarjeta->Fixture->visitante->Nombre;
				}else{
					echo $tarjeta->Fixture->local->Nombre;
				} 
			}else{
				if($tarjeta->Fixture->Local == $tarjeta->Jugador->idEquipo){
					echo $tarjeta->Fixture->visitante->Nombre;
				}else{
					echo $tarjeta->Fixture->local->Nombre;
				} 
			}	
			?>
			</td>
			<td>
				<?php if(!Yii::app()->user->isGuest){
					echo CHtml::link('', 
						Yii::app()->createUrl('tarjetas/update',array('id'=>$tarjeta->idTarjeta, 'idFixture'=>$tarjeta->idFixture, 'idEquipo'=>$tarjeta->Jugador->idEquipo)),
						array('class'=>'icon-edit')) ;
				}?>
			</td>
		</tr>
	<?php }?>	
	</table>
<?php }
?>

<?php
if(isset($rojas)){?>
	<table class="table">
	<thead>
		<th>Torneo</th>
		<th>Contra Equipo</th>
		<th>Desde Fecha</th>
		<th>Fecha</th>
		<th>Motivo</th>
		<th></th>
	</thead>
	<?php foreach ($rojas as $tarjeta) {?>
		<tr class="alert alert-error">
			<td><?php echo $tarjeta->Fixture->Torneo->Nombre; ?></td>
			<td>
			<?php 
			if($tarjeta->Fixture->Torneo->Estado == 'F'){
				if($tarjeta->idEquipo == $tarjeta->Fixture->Local){
					echo $tarjeta->Fixture->visitante->Nombre;
				}else{
					echo $tarjeta->Fixture->local->Nombre;
				} 
			}else{
				if($tarjeta->Fixture->Local == $tarjeta->Jugador->idEquipo){
					echo $tarjeta->Fixture->visitante->Nombre;
				}else{
					echo $tarjeta->Fixture->local->Nombre;
				} 
			}	
			?>
			</td>
			<td><?php echo $tarjeta->Fixture->NFecha ;?></td>
			<td><?php echo $tarjeta->Fixture->Fecha ;?></td>
			<td><?php echo $tarjeta->Motivo ;?></td>
			<td>
				<?php if(!Yii::app()->user->isGuest){
					echo CHtml::link('', 
						Yii::app()->createUrl('tarjetas/update',array('id'=>$tarjeta->idTarjeta, 'idFixture'=>$tarjeta->idFixture, 'idEquipo'=>$tarjeta->Jugador->idEquipo)),
						array('class'=>'icon-edit')) ;
				}?>
			</td>
		</tr>
	<?php }?>	
	
	</table>
<?php }
?>

<?php
if(isset($goles)){?>
	<table class="table">
	<thead>
		<th>Equipo al que hizo</th>
		<th>Fecha</th>
		<th>Cantidad</th>
		<th></th>
	</thead>
	<?php
	foreach ($goles as $gol) {?>
		<tr>
			<td><?php 
			if($gol->Fixture->Torneo->Estado == 'F'){
				if($gol->Fixture->Local == $gol->Fixture->Local){
					echo $gol->Fixture->visitante->Nombre;
				}else{
					echo $gol->Fixture->local->Nombre;
				} 
			}else{
				if($gol->Fixture->Local == $gol->Jugador->idEquipo){
					echo $gol->Fixture->visitante->Nombre;
				}else{
					echo $gol->Fixture->local->Nombre;
				} 
			}	
			?>
			</td>
			<td><?php echo $gol->Fixture->NFecha;?></td>
			<td><?php echo $gol->Cantidad;?></td>
			<td>
				<?php if(!Yii::app()->user->isGuest){
					echo CHtml::link('', 
						Yii::app()->createUrl('fixture/update',array('id'=>$tarjeta->idFixture)),
						array('class'=>'icon-edit')) ;
				}?>
			</td>
		</tr>
	<?php }?>
	</table>
<?php }
?>