<?php
$script = __DIR__ . '/../../scripts/validate-staging.ps1';

function assertScriptContains($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\nMissing: " . $needle . "\n");
        exit(1);
    }
}

if (!file_exists($script)) {
    fwrite(STDERR, "Missing validate-staging.ps1\n");
    exit(1);
}

$content = file_get_contents($script);
assertScriptContains('build-manifest.json', $content, 'staging validator checks build manifest');
assertScriptContains('README_DEPLOY.txt', $content, 'staging validator checks deploy readme is present');
assertScriptContains('Assert-NotExists (Join-Path $apiRoot ".env")', $content, 'staging validator checks secrets are not copied');
assertScriptContains('Join-Path $apiRoot ".env.example"', $content, 'staging validator checks env example is present');
assertScriptContains('vendor\autoload.php', $content, 'staging validator checks composer vendor is present');
assertScriptContains('.htaccess', $content, 'staging validator checks htaccess is present');
assertScriptContains('https://veteranos.ar/nueva_web', $content, 'staging validator checks production API base is bundled');

fwrite(STDOUT, "staging_script_test OK\n");
