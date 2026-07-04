<?php
require __DIR__ . '/../app/lib/env_loader.php';

function assertSameEnvValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

$tmp = tempnam(sys_get_temp_dir(), 'veteranos_env_');
file_put_contents($tmp, implode("\n", [
    '# comment',
    'DB_HOST = localhost',
    'DB_NAME="ye000174_veteranos"',
    "DB_USER='user with spaces'",
    'DB_PASS="abc=123 # not a comment"',
    'EMPTY_VALUE=',
    'BAD_LINE_WITHOUT_EQUALS',
]));

$loaded = \App\Lib\loadEnvFile($tmp);
unlink($tmp);

assertSameEnvValue('localhost', getenv('DB_HOST'), 'loads unquoted values and trims whitespace');
assertSameEnvValue('ye000174_veteranos', getenv('DB_NAME'), 'loads double quoted values without quotes');
assertSameEnvValue('user with spaces', getenv('DB_USER'), 'loads single quoted values without quotes');
assertSameEnvValue('abc=123 # not a comment', getenv('DB_PASS'), 'keeps equals and hashes inside quoted values');
assertSameEnvValue('', getenv('EMPTY_VALUE'), 'loads empty values');
assertSameEnvValue([
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PASS',
    'EMPTY_VALUE',
], $loaded, 'returns loaded keys only');

fwrite(STDOUT, "env_loader_test OK\n");
