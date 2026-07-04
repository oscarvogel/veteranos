<?php
$originalDebug = getenv('API_DEBUG');

function loadSettingsWithDebug($value) {
    if ($value === null) {
        putenv('API_DEBUG');
    } else {
        putenv('API_DEBUG=' . $value);
    }

    return require __DIR__ . '/../src/settings.php';
}

function assertDisplayErrorDetails($expected, $value, $message) {
    $settings = loadSettingsWithDebug($value);
    $actual = $settings['settings']['displayErrorDetails'];

    if ($actual !== $expected) {
        fwrite(STDERR, $message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

assertDisplayErrorDetails(false, null, 'API_DEBUG unset keeps Slim details hidden');
assertDisplayErrorDetails(false, 'false', 'API_DEBUG=false keeps Slim details hidden');
assertDisplayErrorDetails(true, 'true', 'API_DEBUG=true enables Slim details');
assertDisplayErrorDetails(true, '1', 'API_DEBUG=1 enables Slim details');

if ($originalDebug === false) {
    putenv('API_DEBUG');
} else {
    putenv('API_DEBUG=' . $originalDebug);
}

fwrite(STDOUT, "settings_test OK\n");
