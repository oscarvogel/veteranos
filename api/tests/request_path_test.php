<?php
require __DIR__ . '/../app/lib/request_path.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

$server = [
    'REQUEST_URI' => '/nueva_web/api/fixture?torneo_id=1',
    'SCRIPT_NAME' => '/nueva_web/api/public/index.php',
    'PHP_SELF' => '/nueva_web/api/public/index.php',
];

\App\Lib\normalizeApiRequestPath($server);
assertSameValue('/api/fixture?torneo_id=1', $server['REQUEST_URI'], 'strips deployment prefix before /api');
assertSameValue('/api/public/index.php', $server['SCRIPT_NAME'], 'normalizes script name with api prefix');
assertSameValue('/api/public/index.php', $server['PHP_SELF'], 'normalizes php self with api prefix');

$server = ['REQUEST_URI' => '/api/fixture?torneo_id=1'];
\App\Lib\normalizeApiRequestPath($server);
assertSameValue('/api/fixture?torneo_id=1', $server['REQUEST_URI'], 'keeps already-normal api URI');

$server = ['REQUEST_URI' => '/torneo/'];
\App\Lib\normalizeApiRequestPath($server);
assertSameValue('/torneo/', $server['REQUEST_URI'], 'keeps legacy routes untouched');

fwrite(STDOUT, "request_path_test OK\n");
