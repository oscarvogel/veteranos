<?php

$route = file_get_contents(__DIR__ . '/../app/route/modern_api_route.php');

function assertContainsText($needle, $haystack, $message) {
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . "\nMissing: " . $needle . "\n");
        exit(1);
    }
}

assertContainsText("\$this->get('/jugador/importar-lista-buena-fe'", $route, 'GET import route is registered');
assertContainsText("withHeader('Location', '/index.php?r=jugador/importarListaBuenaFe')", $route, 'GET import route redirects to the Yii form');

fwrite(STDOUT, "import_form_route_test OK\n");
