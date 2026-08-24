<?php

$route = file_get_contents(__DIR__ . '/../app/route/planillas_import_route.php');
$model = file_get_contents(__DIR__ . '/../app/model/planilla_import_model.php');
$routes = file_get_contents(__DIR__ . '/../src/routes.php');
$env = file_get_contents(__DIR__ . '/../.env.example');

function assertPlanillaContains($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\nMissing: " . $needle . "\n");
        exit(1);
    }
}

assertPlanillaContains("/api/planillas", $route, 'Planilla API group is defined');
assertPlanillaContains("/preview", $route, 'Preview endpoint is defined');
assertPlanillaContains("/confirmar", $route, 'Confirm endpoint is defined');
assertPlanillaContains("PlanillasApiGuard::authorized", $route, 'Endpoints are protected');
assertPlanillaContains("planillas_import_route.php", $routes, 'Planilla routes are registered');
assertPlanillaContains("beginTransaction()", $model, 'Confirm uses a DB transaction');
assertPlanillaContains("rollBack()", $model, 'Confirm rolls back on errors');
assertPlanillaContains("hash_hmac('sha256'", $model, 'Preview/confirm payload is signed');
assertPlanillaContains("DELETE FROM goles WHERE idFixture=? AND idJugador=?", $model, 'Goals are replaced idempotently');
assertPlanillaContains("API_PLANILLAS_KEY=", $env, 'API key is documented in env example');
assertPlanillaContains("API_PLANILLAS_SECRET=", $env, 'Signing secret is documented in env example');

fwrite(STDOUT, "planillas_import_test OK\n");
