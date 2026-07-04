<?php
$base = getenv('API_BASE_URL') ?: 'http://127.0.0.1:8017';
$paths = ['/api/health', '/nueva_web/api/health'];

foreach ($paths as $path) {
    $url = rtrim($base, '/') . $path;
    $body = @file_get_contents($url);
    if ($body === false) {
        fwrite(STDERR, "No response from {$url}\n");
        exit(1);
    }

    $payload = json_decode($body, true);
    if (!is_array($payload) || empty($payload['success']) || empty($payload['data']['status']) || $payload['data']['status'] !== 'ok') {
        fwrite(STDERR, "Invalid health payload from {$url}: {$body}\n");
        exit(1);
    }
}

fwrite(STDOUT, "health_http_test OK\n");
