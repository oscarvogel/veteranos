<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');

function failCuotas($message)
{
    fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
    exit(1);
}

function assertCuotas($condition, $message)
{
    if(!$condition)
        failCuotas($message);
}

function assertCuotasSame($expected, $actual, $message)
{
    if((string)$expected !== (string)$actual)
        failCuotas($message . " Expected " . var_export($expected, true) . " got " . var_export($actual, true));
}

$db = Yii::app()->db;
$jugadorTable = $db->schema->getTable('jugador');
assertCuotas($jugadorTable !== null, 'Debe existir la tabla jugador');
assertCuotas(isset($jugadorTable->columns['es_socio']), 'jugador debe tener columna es_socio');

$pagoTable = $db->schema->getTable('cuota_social_pago');
assertCuotas($pagoTable !== null, 'Debe existir la tabla cuota_social_pago');
foreach(array('idPago', 'idJugador', 'periodo', 'fecha_pago', 'idUsuario', 'observacion', 'created_at', 'updated_at') as $column) {
    assertCuotas(isset($pagoTable->columns[$column]), 'cuota_social_pago debe tener columna ' . $column);
}

$uniqueRows = $db->createCommand(
    "SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'cuota_social_pago'
      AND index_name = 'ux_cuota_social_pago_jugador_periodo'
      AND non_unique = 0"
)->queryScalar();
assertCuotas((int)$uniqueRows >= 2, 'Debe existir indice unico por idJugador y periodo');

assertCuotas(class_exists('CuotaSocialPago'), 'Debe existir el modelo CuotaSocialPago');
foreach(array('marcarPagosPeriodo', 'getEstadoEquipo', 'getInformePeriodo', 'normalizarPeriodo') as $method) {
    assertCuotas(method_exists('CuotaSocialPago', $method), 'CuotaSocialPago::' . $method . ' debe existir');
}

$equipoRow = $db->createCommand()
    ->select('idEquipo')
    ->from('jugador')
    ->where('idEquipo > 0')
    ->group('idEquipo')
    ->having('COUNT(*) >= 2')
    ->order('COUNT(*) DESC')
    ->queryRow();
assertCuotas($equipoRow !== false, 'Debe existir un equipo real con al menos dos jugadores para probar cuotas');
$equipo = Equipos::model()->findByPk($equipoRow['idEquipo']);
assertCuotas($equipo !== null, 'Debe existir el equipo de prueba');

$jugadores = Jugador::model()->findAllByAttributes(array('idEquipo'=>$equipo->idEquipo), array('limit'=>3, 'order'=>'Nombre ASC'));

$periodoTodos = '2099-01';
$periodoAlgunos = '2099-02';
$jugadorIds = array();
$sociosOriginales = array();
foreach($jugadores as $jugador)
{
    $jugadorIds[] = (int)$jugador->idJugador;
    $sociosOriginales[(int)$jugador->idJugador] = (int)$jugador->es_socio;
}

$db->createCommand()->delete(
    'cuota_social_pago',
    array('and', array('in', 'idJugador', $jugadorIds), array('in', 'periodo', array($periodoTodos, $periodoAlgunos)))
);
$db->createCommand()->update('jugador', array('es_socio'=>0), array('in', 'idJugador', $jugadorIds));

$socioIds = array($jugadorIds[0], $jugadorIds[1]);
$db->createCommand()->update('jugador', array('es_socio'=>1), array('in', 'idJugador', $socioIds));

$resultado = CuotaSocialPago::marcarPagosPeriodo($equipo->idEquipo, $periodoTodos, $socioIds, $socioIds, 1);
assertCuotasSame(2, $resultado['pagados'], 'Marcar todos debe dejar dos socios pagados');

$resultadoDuplicado = CuotaSocialPago::marcarPagosPeriodo($equipo->idEquipo, $periodoTodos, $socioIds, $socioIds, 1);
assertCuotasSame(2, $resultadoDuplicado['pagados'], 'Marcar dos veces no debe duplicar pagos');
$cantidadPagos = $db->createCommand()
    ->select('COUNT(*)')
    ->from('cuota_social_pago')
    ->where(array('and', 'periodo = :periodo', array('in', 'idJugador', $socioIds)), array(':periodo'=>$periodoTodos))
    ->queryScalar();
assertCuotasSame(2, $cantidadPagos, 'Debe haber un pago por socio y periodo');

$resultadoAlgunos = CuotaSocialPago::marcarPagosPeriodo($equipo->idEquipo, $periodoAlgunos, $socioIds, array($socioIds[0]), 1);
assertCuotasSame(1, $resultadoAlgunos['pagados'], 'Marcar algunos debe dejar solo un socio pagado');

$informe = CuotaSocialPago::getInformePeriodo($periodoAlgunos, $equipo->idEquipo, '');
assertCuotasSame(1, count($informe['alDia']), 'El informe debe detectar un socio al dia');
assertCuotasSame(1, count($informe['pendientes']), 'El informe debe detectar un socio pendiente');
assertCuotas(count($informe['noSocios']) >= 0, 'El informe debe devolver lista de no socios');

$estado = CuotaSocialPago::getEstadoEquipo($equipo->idEquipo, $periodoAlgunos);
assertCuotasSame($periodoAlgunos, $estado['periodo'], 'El estado publico debe conservar el periodo');
assertCuotas(isset($estado['equipo']['Nombre']), 'El estado publico debe incluir nombre del equipo');
assertCuotas(count($estado['jugadores']) >= 2, 'El estado publico debe incluir jugadores del equipo');
foreach($estado['jugadores'] as $row) {
    assertCuotas(isset($row['Nombre']), 'El estado publico debe incluir nombre del jugador');
    assertCuotas(isset($row['esSocio']), 'El estado publico debe incluir condicion de socio');
    assertCuotas(isset($row['estado']), 'El estado publico debe incluir estado de cuota');
    assertCuotas(!isset($row['DNI']), 'El estado publico no debe exponer DNI');
    assertCuotas(!isset($row['Observacion']), 'El estado publico no debe exponer observaciones internas');
}

$db->createCommand()->delete(
    'cuota_social_pago',
    array('and', array('in', 'idJugador', $jugadorIds), array('in', 'periodo', array($periodoTodos, $periodoAlgunos)))
);
foreach($sociosOriginales as $idJugador=>$esSocioOriginal) {
    $db->createCommand()->update('jugador', array('es_socio'=>$esSocioOriginal), 'idJugador = :idJugador', array(':idJugador'=>$idJugador));
}

echo "OK cuotas sociales" . PHP_EOL;
