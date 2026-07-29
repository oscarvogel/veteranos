<?php
if (!function_exists('listaBuenaFeExcelFechaNacimiento')) {
function listaBuenaFeExcelFechaNacimiento($fecha) {
	$fecha = trim((string)$fecha);
	if ($fecha === '') {
		return '';
	}
	if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $matches)) {
		return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
	}
	return $fecha;
}
}

if(isset($jugadores)){
	$num = 1;
	$tecnico = isset($equipo->Tecnico) ? $equipo->Tecnico : '';
	$ayudanteTecnico = isset($equipo->AyudanteTecnico) ? $equipo->AyudanteTecnico : '';
	?>
	<h5>ASOCIACION DE FUTBOL DE VETERANOS, SUPER Y SENIOR "LDOR GRAL SAN MARTIN"</h5>
	<table border="1">
		<tr>
			<th>T&eacute;cnico</th>
			<td><?php echo CHtml::encode($tecnico); ?></td>
		</tr>
		<tr>
			<th>Ayudante de t&eacute;cnico</th>
			<td><?php echo CHtml::encode($ayudanteTecnico); ?></td>
		</tr>
	</table>
	<table class="table" border="1">
		<thead>
			<th>Nº</th>
			<th>Ing</th>
			<th>Nombre</th>
			<th>Fecha de nacimiento</th>
			<th>DNI</th>
			<th>Observaciones</th>
			<th>Certificado</th>
			<th>Firmo lista</th>
			<th>Fotocopia DNI</th>
			<th>Declaracion Jurada</th>
		</thead>
	<?php
	foreach ($jugadores as $jugador) {?>
		<tr>
			<td><?php echo $num++;?></td>
			<td></td>
			<td><?php echo htmlentities($jugador->Nombre, ENT_QUOTES,'UTF-8');?></td>
			<td><?php echo CHtml::encode(listaBuenaFeExcelFechaNacimiento($jugador->fecha_nacimiento));?></td>
			<td><?php echo CHtml::encode($jugador->DNI);?></td>
			<td><?php echo CHtml::encode($jugador->Observacion);?></td>
			<td><?php echo $jugador->certificado ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->firma_lista ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->fotocopia_dni ? 'SI' : 'NO';?></td>
			<td><?php echo $jugador->dec_jurada ? 'SI' : 'NO';?></td>
		</tr>
			
	
	<?php }?>
	</table>
<?php 
$data = array(
    1 => array ('Name', 'Surname'),
    array('Schwarz', 'Oliver'),
    array('Test', 'Peter')
);
Yii::import('application.extensions.phpexcel.JPhpExcel');
$xls = new JPhpExcel('UTF-8', false, 'Jugadores');
//$xls->addArray($data);
$xls->generateXML($equipo->Nombre);


}
?>
