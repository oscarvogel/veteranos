<?php
$script = __DIR__ . '/../../scripts/verify-modernizacion.ps1';

function assertContainsText($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\nMissing: " . $needle . "\n");
        exit(1);
    }
}

if (!file_exists($script)) {
    fwrite(STDERR, "Missing verify-modernizacion.ps1\n");
    exit(1);
}

$content = file_get_contents($script);
assertContainsText('modern_api_response_test.php', $content, 'verify script runs modern API response tests');
assertContainsText('env_loader_test.php', $content, 'verify script runs env loader tests');
assertContainsText('request_path_test.php', $content, 'verify script runs request path tests');
assertContainsText('settings_test.php', $content, 'verify script runs settings tests');
assertContainsText('health_http_test.php', $content, 'verify script runs health HTTP tests');
assertContainsText('npm test', $content, 'verify script runs frontend tests');
assertContainsText('npm audit', $content, 'verify script runs frontend audit');
assertContainsText('build-nueva-web.ps1', $content, 'verify script builds staging package');
assertContainsText('package_script_test.php', $content, 'verify script validates package script');
assertContainsText('validate-staging.ps1', $content, 'verify script validates staging package');
assertContainsText('test-nueva-web.ps1', $content, 'verify script runs smoke test');
assertContainsText('RequireDatabase', $content, 'verify script supports DB-required mode');

fwrite(STDOUT, "verify_script_test OK\n");
