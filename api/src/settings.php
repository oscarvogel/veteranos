<?php
$apiDebug = getenv('API_DEBUG');
$displayErrorDetails = in_array(strtolower(trim((string)$apiDebug)), ['1', 'true', 'yes', 'on'], true);

return [
    'settings' => [
        'displayErrorDetails' => $displayErrorDetails,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
