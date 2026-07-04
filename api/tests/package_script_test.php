<?php
$script = __DIR__ . '/../../scripts/package-nueva-web.ps1';

function assertPackageScriptContains($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\nMissing: " . $needle . "\n");
        exit(1);
    }
}

if (!file_exists($script)) {
    fwrite(STDERR, "Missing package-nueva-web.ps1\n");
    exit(1);
}

$content = file_get_contents($script);
assertPackageScriptContains('build-nueva-web.ps1', $content, 'package script builds staging before zipping');
assertPackageScriptContains('validate-staging.ps1', $content, 'package script validates staging before zipping');
assertPackageScriptContains('Compress-Archive', $content, 'package script creates a zip archive');
assertPackageScriptContains('nueva_web.zip', $content, 'package script writes expected zip name by default');
assertPackageScriptContains('api\.env', $content, 'package script guards against copying secrets');
assertPackageScriptContains('System.IO.Compression.ZipFile', $content, 'package script validates zip entries after creation');
assertPackageScriptContains('api/vendor/autoload.php', $content, 'package script validates vendor entry in zip');

fwrite(STDOUT, "package_script_test OK\n");
