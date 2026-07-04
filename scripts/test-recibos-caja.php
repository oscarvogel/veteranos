<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');

function assertTrue($condition, $message)
{
    if(!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

function assertSameValue($expected, $actual, $message)
{
    if((string)$expected !== (string)$actual) {
        fwrite(STDERR, "FAIL: " . $message . " Expected " . var_export($expected, true) . " got " . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$db = Yii::app()->db;
$requiredColumns = array('NumeroRecibo', 'Estado', 'idUsuario', 'FechaAlta', 'FechaAnulacion', 'MotivoAnulacion');
$columns = array_keys($db->schema->getTable('ingresos')->columns);
foreach($requiredColumns as $column) {
    assertTrue(in_array($column, $columns), 'ingresos debe tener columna ' . $column);
}

assertTrue(method_exists('Ingresos', 'siguienteNumeroRecibo'), 'Ingresos::siguienteNumeroRecibo debe existir');
assertTrue(method_exists('Ingresos', 'getArqueoCaja'), 'Ingresos::getArqueoCaja debe existir');
assertTrue(method_exists('Ingresos', 'getTotalArqueoCaja'), 'Ingresos::getTotalArqueoCaja debe existir');
assertTrue(method_exists('Ingresos', 'anular'), 'Ingresos::anular debe existir');

$conceptos = CHtml::listData(Conceptos::model()->findAll(), 'Nombre', 'idConcepto');
foreach(array('Arancel semanal', 'Cuota societaria', 'Multas') as $concepto) {
    assertTrue(isset($conceptos[$concepto]), 'Debe existir el concepto ' . $concepto);
}

$equipo = Equipos::model()->find();
assertTrue($equipo !== null, 'Debe existir al menos un equipo para probar recibos');

$usuarioId = (int)$db->createCommand('SELECT iduser FROM cruge_user ORDER BY iduser LIMIT 1')->queryScalar();
assertTrue($usuarioId > 0, 'Debe existir al menos un usuario Cruge para cobrador');

$db->createCommand("DELETE FROM ingresos WHERE Detalle LIKE 'TEST RECIBO %'")->execute();

$maxBefore = (int)$db->createCommand('SELECT COALESCE(MAX(NumeroRecibo), 0) FROM ingresos')->queryScalar();
$expectedNumero = $maxBefore + 1;

$ingreso = new Ingresos;
$ingreso->idEquipo = $equipo->idEquipo;
$ingreso->idConcepto = $conceptos['Arancel semanal'];
$ingreso->Fecha = '2026-01-15';
$ingreso->Hora = '10:00:00';
$ingreso->Monto = 150;
$ingreso->Detalle = 'TEST RECIBO CAJA ALTA';
$ingreso->Estado = 'VIGENTE';
$ingreso->idUsuario = $usuarioId;
$ingreso->FechaAlta = date('Y-m-d H:i:s');
$ingreso->NumeroRecibo = Ingresos::siguienteNumeroRecibo();

assertSameValue($expectedNumero, $ingreso->NumeroRecibo, 'El proximo numero de recibo debe ser correlativo');
assertTrue($ingreso->save(), 'Debe guardar recibo valido: ' . print_r($ingreso->getErrors(), true));

$total = Ingresos::getTotalArqueoCaja($ingreso->Fecha, $ingreso->Fecha, $usuarioId);
assertSameValue('150.00', number_format((float)$total, 2, '.', ''), 'El arqueo debe sumar recibos vigentes del cobrador');

assertTrue($ingreso->anular('TEST RECIBO CAJA ANULACION'), 'Debe anular recibo vigente: ' . print_r($ingreso->getErrors(), true));
$ingreso = Ingresos::model()->findByPk($ingreso->idIngreso);
assertSameValue('ANULADO', $ingreso->Estado, 'El recibo debe quedar anulado');
assertTrue($ingreso->FechaAnulacion !== null && $ingreso->FechaAnulacion !== '', 'La anulacion debe registrar fecha');

$total = Ingresos::getTotalArqueoCaja($ingreso->Fecha, $ingreso->Fecha, $usuarioId);
assertSameValue('0.00', number_format((float)$total, 2, '.', ''), 'El arqueo no debe sumar recibos anulados');

echo "OK recibos caja" . PHP_EOL;
