<?php

require_once __DIR__ . '/../app/lib/database.php';
require_once __DIR__ . '/../app/model/planilla_import_model.php';

use App\Model\PlanillaImportModel;

putenv('API_PLANILLAS_SECRET=test-secret');

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$db->exec('CREATE TABLE torneo (idTorneo INTEGER PRIMARY KEY, Nombre TEXT)');
$db->exec('CREATE TABLE equipos (idEquipo INTEGER PRIMARY KEY, Nombre TEXT)');
$db->exec('CREATE TABLE jugador (idJugador INTEGER PRIMARY KEY, Nombre TEXT, DNI TEXT, idEquipo INTEGER)');
$db->exec('CREATE TABLE fixture (
    idFixture INTEGER PRIMARY KEY,
    idTorneo INTEGER,
    NFecha INTEGER,
    Fecha TEXT,
    Local INTEGER,
    Visitante INTEGER,
    GolLocal INTEGER,
    GolVisitante INTEGER,
    PuntosLocal INTEGER,
    PuntosVisitante INTEGER
)');
$db->exec('CREATE TABLE goles (idGol INTEGER PRIMARY KEY AUTOINCREMENT, idJugador INTEGER, idFixture INTEGER, Cantidad INTEGER)');
$db->exec('CREATE TABLE tarjetas (
    idTarjeta INTEGER PRIMARY KEY AUTOINCREMENT,
    idFixture INTEGER,
    idJugador INTEGER,
    Amarilla INTEGER,
    Roja INTEGER,
    DesdeFecha TEXT,
    HastaFecha INTEGER,
    Motivo TEXT,
    idEquipo INTEGER
)');

$db->exec("INSERT INTO torneo VALUES (1, 'Clausura 2026')");
$db->exec("INSERT INTO equipos VALUES (10, 'MANDIYU SENIOR')");
$db->exec("INSERT INTO equipos VALUES (20, 'RIVAL DE PRUEBA')");
$db->exec("INSERT INTO jugador VALUES (1001, 'Acuña Ignacio Oscar', '24701343', 10)");
$db->exec("INSERT INTO jugador VALUES (1002, 'Canale Emilio Marcelo', '25205266', 10)");
$db->exec("INSERT INTO jugador VALUES (2001, 'Jugador Rival', '30000001', 20)");
$db->exec("INSERT INTO fixture (idFixture,idTorneo,NFecha,Fecha,Local,Visitante) VALUES (500,1,3,'2026-08-22',10,20)");

$model = new PlanillaImportModel($db);

// Caso basado en la planilla de MANDIYU SENIOR del PDF del 22/08/2026.
// Se usan identidades/DNI visibles en la planilla real. No se infiere el rival:
// el fixture se referencia por idFixture y se prueba el resultado unilateral.
$mandiyuOnly = [
    'version' => 1,
    'source_id' => 'camscanner-2026-08-23-p6',
    'torneo' => ['id' => 1],
    'partidos' => [[
        'idFixture' => 500,
        'equipo' => ['id' => 10],
        'resultado' => ['goles_equipo' => 1],
        'jugadores' => [[
            'documento' => '24701343',
            'nombre' => 'Acuña Ignacio Oscar',
            'goles' => 1,
            'amarillas' => 0,
            'rojas' => 0,
            'confianza' => 0.99,
        ]]
    ]]
];

$preview = $model->Preview($mandiyuOnly);
if ($preview['success'] !== false) {
    fwrite(STDERR, "Expected incomplete one-sided result to block success\n");
    exit(1);
}
if (empty($preview['resolved']['partidos'][0]['jugadores'][0]['idJugador']) ||
    (int)$preview['resolved']['partidos'][0]['jugadores'][0]['idJugador'] !== 1001) {
    fwrite(STDERR, "DNI matching for real Mandiyu player failed\n");
    exit(1);
}

// Agregamos la planilla opuesta del mismo fixture. El parser debe consolidar ambas
// páginas en un único partido y completar local/visitante sin duplicarlo.
$paired = $mandiyuOnly;
$paired['source_id'] = 'camscanner-2026-08-23-p6-paired';
$paired['partidos'][] = [
    'idFixture' => 500,
    'equipo' => ['id' => 20],
    'resultado' => ['goles_equipo' => 0],
    'jugadores' => []
];

$preview2 = $model->Preview($paired);
if ($preview2['success'] !== true || $preview2['can_confirm'] !== true) {
    fwrite(STDERR, "Paired sheets should produce a confirmable preview\n");
    var_export($preview2);
    exit(1);
}
if (count($preview2['resolved']['partidos']) !== 1) {
    fwrite(STDERR, "Duplicate fixture sheets were not consolidated\n");
    exit(1);
}
if ((int)$preview2['resolved']['partidos'][0]['GolLocal'] !== 1 ||
    (int)$preview2['resolved']['partidos'][0]['GolVisitante'] !== 0) {
    fwrite(STDERR, "Paired one-sided scores were not merged correctly\n");
    exit(1);
}

// Contradicción: la segunda planilla afirma que el local hizo 2.
$contradictory = $paired;
$contradictory['source_id'] = 'camscanner-2026-08-23-contradiction';
$contradictory['partidos'][1]['resultado']['goles_rival'] = 2;
$preview3 = $model->Preview($contradictory);
if ($preview3['success'] !== false) {
    fwrite(STDERR, "Contradictory sheets must be rejected\n");
    exit(1);
}

fwrite(STDOUT, "planillas_import_integration_test OK\n");
