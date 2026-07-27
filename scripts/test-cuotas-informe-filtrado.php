<?php

$controller = file_get_contents(dirname(__FILE__) . '/../protected/controllers/SociosCuotaController.php');
$view = file_get_contents(dirname(__FILE__) . '/../protected/views/sociosCuota/informe.php');

if(strpos($controller, '$hayFiltro') === false) {
    fwrite(STDERR, "FAIL: el controlador debe calcular hayFiltro antes de consultar informe" . PHP_EOL);
    exit(1);
}

if(strpos($controller, 'new CArrayDataProvider') === false) {
    fwrite(STDERR, "FAIL: el informe debe usar CArrayDataProvider paginado" . PHP_EOL);
    exit(1);
}

if(strpos($controller, "'pageSize'=>50") === false && strpos($controller, '"pageSize"=>50') === false) {
    fwrite(STDERR, "FAIL: el informe debe paginar de a 50 registros" . PHP_EOL);
    exit(1);
}

if(strpos($view, 'Seleccione un equipo o un estado') === false) {
    fwrite(STDERR, "FAIL: la vista debe pedir filtro antes de listar" . PHP_EOL);
    exit(1);
}

if(strpos($view, 'cuotas-informe-grid') === false) {
    fwrite(STDERR, "FAIL: la vista debe renderizar una grilla paginada" . PHP_EOL);
    exit(1);
}

echo "OK cuotas informe filtrado" . PHP_EOL;
