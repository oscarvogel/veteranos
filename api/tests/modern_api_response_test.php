<?php
require __DIR__ . '/../app/lib/modern_api_response.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

$items = range(1, 25);
$page = \App\Lib\ModernApiResponse::paginate($items, ['page' => 2, 'per_page' => 10]);
assertSameValue([11, 12, 13, 14, 15, 16, 17, 18, 19, 20], $page['data'], 'paginate returns second page data');
assertSameValue(['total' => 25, 'per_page' => 10, 'page' => 2, 'total_pages' => 3], $page['meta'], 'paginate returns expected metadata');

$legacy = new stdClass();
$legacy->response = true;
$legacy->result = [['idTorneo' => 9, 'Nombre' => 'Apertura', 'Estado' => 'I']];
$payload = \App\Lib\ModernApiResponse::fromLegacy($legacy, function ($row) {
    return ['id' => $row['idTorneo'], 'nombre' => $row['Nombre'], 'estado' => $row['Estado']];
});
assertSameValue(true, $payload['success'], 'fromLegacy preserves success flag');
assertSameValue([['id' => 9, 'nombre' => 'Apertura', 'estado' => 'I']], $payload['data'], 'fromLegacy maps data');

$legacy->response = false;
$legacy->message = 'Fallo controlado';
$payload = \App\Lib\ModernApiResponse::fromLegacy($legacy);
assertSameValue(false, $payload['success'], 'fromLegacy preserves failure flag');
assertSameValue('Fallo controlado', $payload['error']['message'], 'fromLegacy exposes error message');

$legacy->message = "SQLSTATE[HY000] [1044] Access denied for user 'demo'@'localhost' to database 'secret_db'";
putenv('API_DEBUG=false');
$payload = \App\Lib\ModernApiResponse::fromLegacy($legacy);
assertSameValue('No se pudo completar la consulta.', $payload['error']['message'], 'fromLegacy hides database details by default');

putenv('API_DEBUG=true');
$payload = \App\Lib\ModernApiResponse::fromLegacy($legacy);
assertSameValue($legacy->message, $payload['error']['message'], 'fromLegacy exposes details when API_DEBUG is true');
putenv('API_DEBUG');

fwrite(STDOUT, "modern_api_response_test OK\n");
