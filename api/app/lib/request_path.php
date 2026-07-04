<?php
namespace App\Lib;

function normalizeApiRequestPath(&$server)
{
    if (empty($server['REQUEST_URI'])) {
        return;
    }

    $requestUri = $server['REQUEST_URI'];
    $apiPos = strpos($requestUri, '/api/');

    if ($apiPos === false) {
        return;
    }

    if ($apiPos > 0) {
        $server['REQUEST_URI'] = substr($requestUri, $apiPos);
    }

    foreach (['SCRIPT_NAME', 'PHP_SELF'] as $key) {
        if (!empty($server[$key])) {
            $scriptApiPos = strpos($server[$key], '/api/');
            if ($scriptApiPos !== false && $scriptApiPos > 0) {
                $server[$key] = substr($server[$key], $scriptApiPos);
            }
        }
    }
}
