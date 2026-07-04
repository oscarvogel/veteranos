<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');
require_once dirname(__FILE__) . '/../protected/components/Controller.php';
require_once dirname(__FILE__) . '/../protected/controllers/JugadorController.php';

function assertLegajo($condition, $message)
{
    if(!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

function assertSameLegajo($expected, $actual, $message)
{
    if($expected !== $actual) {
        fwrite(STDERR, "FAIL: " . $message . " Expected " . var_export($expected, true) . " got " . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$db = Yii::app()->db;
$table = $db->schema->getTable('jugador_documento');
assertLegajo($table !== null, 'Debe existir la tabla jugador_documento');

foreach(array('idDocumento', 'idJugador', 'tipo', 'titulo', 'archivo_original', 'archivo_guardado', 'mime_type', 'extension', 'tamano_bytes', 'observacion', 'idUsuario', 'created_at', 'updated_at') as $column) {
    assertLegajo(isset($table->columns[$column]), 'jugador_documento debe tener columna ' . $column);
}

assertLegajo(class_exists('JugadorDocumento'), 'Debe existir el modelo JugadorDocumento');
assertSameLegajo(array('dni', 'certificado_salud', 'firma_lista', 'declaracion_jurada', 'adicional'), array_keys(JugadorDocumento::getTipos()), 'Los tipos de legajo deben ser flexibles y conocidos');
assertLegajo(JugadorDocumento::esExtensionPermitida('pdf'), 'Debe permitir PDF');
assertLegajo(JugadorDocumento::esExtensionPermitida('jpg'), 'Debe permitir JPG');
assertLegajo(JugadorDocumento::esMimePermitido('image/png'), 'Debe permitir PNG por MIME');
assertLegajo(!JugadorDocumento::esExtensionPermitida('php'), 'No debe permitir PHP');
assertLegajo(!JugadorDocumento::esMimePermitido('application/x-php'), 'No debe permitir MIME PHP');
assertSameLegajo('fotocopia_dni', JugadorDocumento::getCampoLegacyPorTipo('dni'), 'DNI sincroniza fotocopia_dni');
assertSameLegajo('certificado', JugadorDocumento::getCampoLegacyPorTipo('certificado_salud'), 'Certificado sincroniza certificado');
assertSameLegajo(null, JugadorDocumento::getCampoLegacyPorTipo('adicional'), 'Adicional no sincroniza campo legacy');

$jugador = Jugador::model()->find();
assertLegajo($jugador !== null, 'Debe existir al menos un jugador para probar legajo');

$basePath = JugadorDocumento::getBaseStoragePath();
assertLegajo(strpos($basePath, Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime') === 0, 'Los legajos deben guardarse bajo protected/runtime');
assertLegajo(strpos($basePath, dirname(Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'media') !== 0, 'Los legajos no deben guardarse bajo media publica');

$storedName = JugadorDocumento::generarNombreGuardado($jugador->idJugador, 'dni', 'DNI Frente.pdf');
assertLegajo(preg_match('/^jugador-' . preg_quote($jugador->idJugador, '/') . '-dni-[0-9]{14}-[a-f0-9]{8}\.pdf$/', $storedName) === 1, 'El nombre guardado debe ser estable, privado y conservar extension permitida');

$controller = new JugadorController('jugador');
foreach(array('actionLegajo', 'actionSubirDocumento', 'actionDescargarDocumento', 'actionEliminarDocumento') as $method) {
    assertLegajo(method_exists($controller, $method), 'JugadorController debe implementar ' . $method);
}

foreach(array('action_jugador_legajo', 'action_jugador_subirDocumento', 'action_jugador_descargarDocumento', 'action_jugador_eliminarDocumento') as $operation) {
    $exists = (int)$db->createCommand('SELECT COUNT(*) FROM cruge_authitem WHERE name = :name')->queryScalar(array(':name'=>$operation));
    assertLegajo($exists === 1, 'Debe existir permiso Cruge ' . $operation);
    $delegado = (int)$db->createCommand('SELECT COUNT(*) FROM cruge_authitemchild WHERE parent = :parent AND child = :child')->queryScalar(array(':parent'=>'delegado', ':child'=>$operation));
    assertLegajo($delegado === 1, 'El rol delegado debe tener permiso ' . $operation);
}

$db->createCommand('DELETE FROM jugador_documento WHERE idJugador = :idJugador AND archivo_original = :archivo')
    ->execute(array(':idJugador'=>$jugador->idJugador, ':archivo'=>'TEST LEGAJO DNI.pdf'));
JugadorDocumento::sincronizarCamposLegacy($jugador->idJugador);

$dir = JugadorDocumento::asegurarDirectorioJugador($jugador->idJugador);
$documento = new JugadorDocumento;
$documento->idJugador = $jugador->idJugador;
$documento->tipo = JugadorDocumento::TIPO_DNI;
$documento->titulo = 'Test DNI';
$documento->archivo_original = 'TEST LEGAJO DNI.pdf';
$documento->archivo_guardado = JugadorDocumento::generarNombreGuardado($jugador->idJugador, JugadorDocumento::TIPO_DNI, $documento->archivo_original);
$documento->mime_type = 'application/pdf';
$documento->extension = 'pdf';
$documento->tamano_bytes = 15;
$documento->observacion = 'Documento de prueba automatica';
$documento->idUsuario = null;
$path = $dir . DIRECTORY_SEPARATOR . $documento->archivo_guardado;
file_put_contents($path, "%PDF-1.4\n%test\n");
assertLegajo($documento->save(), 'Debe guardar documento valido: ' . print_r($documento->getErrors(), true));

$jugadorActualizado = Jugador::model()->findByPk($jugador->idJugador);
assertSameLegajo('1', (string)$jugadorActualizado->fotocopia_dni, 'Guardar DNI debe marcar fotocopia_dni legacy');
assertLegajo(is_file($path), 'El archivo fisico debe existir antes de eliminar el documento');

assertLegajo($documento->delete(), 'Debe eliminar documento de prueba');
assertLegajo(!is_file($path), 'Eliminar documento debe borrar archivo fisico');

echo "OK legajo jugador documentos" . PHP_EOL;
