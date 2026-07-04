<?php

$controllerPath = __DIR__ . '/../protected/controllers/JugadorController.php';
$source = file_get_contents($controllerPath);

if ($source === false) {
    fwrite(STDERR, "No se pudo leer JugadorController.php\n");
    exit(1);
}

$actionStart = strpos($source, 'public function actionAsignaequipo()');
if ($actionStart === false) {
    fwrite(STDERR, "No se encontro actionAsignaequipo\n");
    exit(1);
}

$actionEnd = strpos($source, 'public function actionListajugadores()', $actionStart);
if ($actionEnd === false) {
    fwrite(STDERR, "No se encontro el fin de actionAsignaequipo\n");
    exit(1);
}

$actionSource = substr($source, $actionStart, $actionEnd - $actionStart);
$idEquipoPartialSaveCount = substr_count($actionSource, "save(false, array('idEquipo'))")
    + substr_count($actionSource, "\$camposGuardar = array('idEquipo')");

if ($idEquipoPartialSaveCount !== 2) {
    fwrite(
        STDERR,
        "actionAsignaequipo debe guardar solo idEquipo en asignar y borrar; encontrados {$idEquipoPartialSaveCount} guardados parciales.\n"
    );
    exit(1);
}

echo "OK: actionAsignaequipo guarda solo idEquipo al asignar y borrar.\n";

$requiredControllerSnippets = array(
    "\$_POST['Jugador']['fecha_nacimiento']" => 'lee la fecha de nacimiento enviada desde el modal',
    "preg_match('/^(\\d{2})\\/(\\d{2})\\/(\\d{4})$/'" => 'valida el formato dd/mm/aaaa',
    'checkdate((int)$matches[2], (int)$matches[1], (int)$matches[3])' => 'rechaza fechas inexistentes',
    "'mostrarModalFechaNacimiento'=>true" => 'reabre el modal cuando la fecha falta o es invalida',
    "\$camposGuardar = array('idEquipo', 'fecha_nacimiento', 'Clase')" => 'guarda fecha, clase y equipo juntos cuando la fecha faltaba',
);

foreach ($requiredControllerSnippets as $snippet => $description) {
    if (strpos($actionSource, $snippet) === false) {
        fwrite(STDERR, "actionAsignaequipo no {$description}.\n");
        exit(1);
    }
}

$fechaFaltanteCheck = strpos($actionSource, "if(trim((string)\$jugador->fecha_nacimiento) === '')");
$asignacionEquipo = strpos($actionSource, '$jugador->idEquipo = $equipo->idEquipo;');
if ($fechaFaltanteCheck === false || $asignacionEquipo === false || $asignacionEquipo < $fechaFaltanteCheck) {
    fwrite(STDERR, "actionAsignaequipo debe asignar idEquipo despues de validar la fecha faltante.\n");
    exit(1);
}

echo "OK: actionAsignaequipo exige fecha de nacimiento valida antes de asignar.\n";

$viewPath = __DIR__ . '/../protected/views/jugador/asignajugador.php';
$viewSource = file_get_contents($viewPath);

if ($viewSource === false) {
    fwrite(STDERR, "No se pudo leer asignajugador.php\n");
    exit(1);
}

if (strpos($viewSource, '$equipoActual') === false || strpos($viewSource, 'Sin equipo') === false) {
    fwrite(STDERR, "asignajugador.php debe mostrar un texto seguro cuando el jugador no tiene equipo.\n");
    exit(1);
}

echo "OK: asignajugador muestra un equipo actual seguro.\n";

$requiredViewSnippets = array(
    'fechaNacimientoModal' => 'incluye el modal de fecha de nacimiento',
    'btnAbrirFechaNacimiento' => 'abre el modal en vez de asignar directo cuando falta la fecha',
    'abrirFechaNacimientoModal' => 'abre el modal con JavaScript nativo sin depender de Bootstrap JS',
    'fechaNacimientoBackdrop' => 'crea un fondo modal sin depender de Bootstrap JS',
    'Jugador[fecha_nacimiento]' => 'envia la fecha de nacimiento al controlador',
    'Formato: dd/mm/aaaa' => 'muestra el formato esperado al usuario',
);

foreach ($requiredViewSnippets as $snippet => $description) {
    if (strpos($viewSource, $snippet) === false) {
        fwrite(STDERR, "asignajugador.php no {$description}.\n");
        exit(1);
    }
}

echo "OK: asignajugador incluye modal obligatorio para fecha faltante.\n";
