<?php

function assertContainsOrden($path, $snippet, $message) {
    $source = file_get_contents($path);
    if ($source === false) {
        fwrite(STDERR, "No se pudo leer {$path}\n");
        exit(1);
    }

    if (strpos($source, $snippet) === false) {
        fwrite(STDERR, $message . "\nFalta: " . $snippet . "\nArchivo: " . $path . "\n");
        exit(1);
    }
}

$root = dirname(__DIR__);
$equiposModel = $root . '/protected/models/Equipos.php';
$equiposController = $root . '/protected/controllers/EquiposController.php';

assertContainsOrden($equiposModel, 'LOWER(TRIM(Nombre)) ASC', 'Equipos::getListEquipo debe ordenar alfabeticamente por nombre normalizado');
assertContainsOrden($equiposController, 'LOWER(TRIM(Nombre)) ASC', 'EquiposController::actionGetEquipos debe consultar ordenado por nombre');
assertContainsOrden($equiposController, "'value' =>", 'GetEquipos debe devolver una lista de objetos con value');
assertContainsOrden($equiposController, "'text' =>", 'GetEquipos debe devolver una lista de objetos con text');
assertContainsOrden($equiposController, 'CJSON::encode($equipos)', 'GetEquipos debe serializar el arreglo ordenado, no CHtml::listData con claves numericas');

echo "OK: combos de equipos usan orden alfabetico estable.\n";
