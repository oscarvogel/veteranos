<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');
require_once dirname(__FILE__) . '/../protected/components/Controller.php';
require_once dirname(__FILE__) . '/../protected/controllers/IngresosController.php';

function assertTruePublico($condition, $message)
{
    if(!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

$db = Yii::app()->db;
$columns = array_keys($db->schema->getTable('ingresos')->columns);
assertTruePublico(in_array('ReciboToken', $columns), 'ingresos debe tener columna ReciboToken');

assertTruePublico(method_exists('Ingresos', 'generarReciboToken'), 'Ingresos::generarReciboToken debe existir');
assertTruePublico(method_exists('Ingresos', 'ensureReciboToken'), 'Ingresos::ensureReciboToken debe existir');
assertTruePublico(method_exists('Ingresos', 'findByReciboToken'), 'Ingresos::findByReciboToken debe existir');
assertTruePublico(method_exists('Ingresos', 'getReciboPublicoUrl'), 'Ingresos::getReciboPublicoUrl debe existir');
assertTruePublico(method_exists('Ingresos', 'getWhatsappUrl'), 'Ingresos::getWhatsappUrl debe existir');

$controller = new IngresosController('ingresos');
assertTruePublico(method_exists($controller, 'actionReciboPublico'), 'IngresosController debe implementar actionReciboPublico');

$filters = $controller->filters();
$filterText = print_r($filters, true);
assertTruePublico(strpos($filterText, 'reciboPublico') !== false && strpos($filterText, '-') !== false, 'reciboPublico debe quedar excluido del filtro Cruge');

$concepto = Conceptos::model()->find();
$equipo = Equipos::model()->find();
assertTruePublico($concepto !== null && $equipo !== null, 'Debe haber equipo y concepto para probar token publico');

$usuarioId = (int)$db->createCommand('SELECT iduser FROM cruge_user ORDER BY iduser LIMIT 1')->queryScalar();
assertTruePublico($usuarioId > 0, 'Debe existir usuario Cruge');

$db->createCommand("DELETE FROM ingresos WHERE Detalle LIKE 'TEST RECIBO PUBLICO %'")->execute();

$ingreso = new Ingresos;
$ingreso->idEquipo = $equipo->idEquipo;
$ingreso->idConcepto = $concepto->idConcepto;
$ingreso->Fecha = '2026-04-10';
$ingreso->Hora = '11:00:00';
$ingreso->Monto = 321;
$ingreso->Detalle = 'TEST RECIBO PUBLICO TOKEN';
$ingreso->Estado = 'VIGENTE';
$ingreso->idUsuario = $usuarioId;
$ingreso->FechaAlta = '2026-04-10 11:00:00';
$ingreso->NumeroRecibo = Ingresos::siguienteNumeroRecibo();
assertTruePublico($ingreso->save(), 'Debe guardar ingreso con token: ' . print_r($ingreso->getErrors(), true));

assertTruePublico(strlen($ingreso->ReciboToken) === 64, 'El token debe tener 64 caracteres');
assertTruePublico(preg_match('/^[a-f0-9]{64}$/', $ingreso->ReciboToken) === 1, 'El token debe ser hexadecimal');
assertTruePublico(Ingresos::findByReciboToken($ingreso->ReciboToken)->idIngreso == $ingreso->idIngreso, 'Debe encontrar el recibo por token');
assertTruePublico(Ingresos::findByReciboToken('token-invalido') === null, 'No debe aceptar tokens invalidos');

$publicUrl = $ingreso->getReciboPublicoUrl();
assertTruePublico(strpos($publicUrl, 'reciboPublico') !== false, 'La URL publica debe apuntar a reciboPublico');
assertTruePublico(strpos($publicUrl, $ingreso->ReciboToken) !== false, 'La URL publica debe incluir el token');

$whatsappUrl = $ingreso->getWhatsappUrl();
assertTruePublico(strpos($whatsappUrl, 'https://wa.me/?text=') === 0, 'La URL de WhatsApp debe usar wa.me');
assertTruePublico(strpos(urldecode($whatsappUrl), $publicUrl) !== false, 'El mensaje de WhatsApp debe incluir la URL publica');

echo "OK recibo publico token" . PHP_EOL;
