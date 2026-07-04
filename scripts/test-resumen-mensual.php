<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');

function assertTrueResumen($condition, $message)
{
    if(!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

function assertMoneyResumen($expected, $actual, $message)
{
    if(number_format((float)$expected, 2, '.', '') !== number_format((float)$actual, 2, '.', '')) {
        fwrite(STDERR, "FAIL: " . $message . " Expected " . $expected . " got " . $actual . PHP_EOL);
        exit(1);
    }
}

assertTrueResumen(method_exists('Ingresos', 'getResumenMensual'), 'Ingresos::getResumenMensual debe existir');

$db = Yii::app()->db;
$conceptos = CHtml::listData(Conceptos::model()->findAll(), 'Nombre', 'idConcepto');
assertTrueResumen(isset($conceptos['Arancel semanal']), 'Debe existir concepto Arancel semanal');
assertTrueResumen(isset($conceptos['Multas']), 'Debe existir concepto Multas');

$equipo = Equipos::model()->find();
assertTrueResumen($equipo !== null, 'Debe existir al menos un equipo para probar resumen mensual');

$usuarioId = (int)$db->createCommand('SELECT iduser FROM cruge_user ORDER BY iduser LIMIT 1')->queryScalar();
assertTrueResumen($usuarioId > 0, 'Debe existir al menos un usuario Cruge');

$db->createCommand("DELETE FROM ingresos WHERE Detalle LIKE 'TEST RESUMEN %'")->execute();

function crearIngresoResumen($equipoId, $conceptoId, $fecha, $monto, $estado, $usuarioId, $detalle)
{
    $ingreso = new Ingresos;
    $ingreso->idEquipo = $equipoId;
    $ingreso->idConcepto = $conceptoId;
    $ingreso->Fecha = $fecha;
    $ingreso->Hora = '09:00:00';
    $ingreso->Monto = $monto;
    $ingreso->Detalle = $detalle;
    $ingreso->Estado = $estado;
    $ingreso->idUsuario = $usuarioId;
    $ingreso->FechaAlta = $fecha . ' 09:00:00';
    $ingreso->NumeroRecibo = Ingresos::siguienteNumeroRecibo();
    assertTrueResumen($ingreso->save(), 'Debe guardar ingreso de prueba: ' . print_r($ingreso->getErrors(), true));
}

crearIngresoResumen($equipo->idEquipo, $conceptos['Arancel semanal'], '2026-02-03', 100, 'VIGENTE', $usuarioId, 'TEST RESUMEN ARANCEL 1');
crearIngresoResumen($equipo->idEquipo, $conceptos['Arancel semanal'], '2026-02-04', 50, 'ANULADO', $usuarioId, 'TEST RESUMEN ARANCEL ANULADO');
crearIngresoResumen($equipo->idEquipo, $conceptos['Multas'], '2026-02-03', 75, 'VIGENTE', $usuarioId, 'TEST RESUMEN MULTA 1');
crearIngresoResumen($equipo->idEquipo, $conceptos['Multas'], '2026-03-01', 999, 'VIGENTE', $usuarioId, 'TEST RESUMEN FUERA MES');

$resumen = Ingresos::getResumenMensual(2026, 2);

assertMoneyResumen(175, $resumen['kpis']['totalVigente'], 'El total vigente mensual debe sumar solo vigentes del mes');
assertMoneyResumen(50, $resumen['kpis']['totalAnulado'], 'El total anulado mensual debe sumar anulados del mes');
assertTrueResumen((int)$resumen['kpis']['cantidadRecibos'] === 3, 'La cantidad de recibos mensual debe incluir vigentes y anulados del mes');
assertMoneyResumen(87.50, $resumen['kpis']['promedioVigente'], 'El promedio vigente debe dividir total vigente por cantidad vigente');

$porConcepto = array();
foreach($resumen['porConcepto'] as $row) {
    $porConcepto[$row['Nombre']] = $row;
}

assertTrueResumen(isset($porConcepto['Arancel semanal']), 'El resumen debe incluir Arancel semanal');
assertTrueResumen(isset($porConcepto['Multas']), 'El resumen debe incluir Multas');
assertMoneyResumen(100, $porConcepto['Arancel semanal']['totalVigente'], 'Arancel semanal vigente');
assertMoneyResumen(50, $porConcepto['Arancel semanal']['totalAnulado'], 'Arancel semanal anulado');
assertMoneyResumen(75, $porConcepto['Multas']['totalVigente'], 'Multas vigente');
assertMoneyResumen(175, $resumen['porDia']['2026-02-03'], 'La serie diaria debe sumar vigentes del dia');

echo "OK resumen mensual" . PHP_EOL;
