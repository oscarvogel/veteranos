<?php

$script = dirname(__FILE__) . '/../migrar-cuotas-sociales-20260709.php';
if(!is_file($script)) {
    fwrite(STDERR, "FAIL: falta migrar-cuotas-sociales-20260709.php" . PHP_EOL);
    exit(1);
}

$contents = file_get_contents($script);
$required = array(
    'MIGRAR_CUOTAS_SOCIALES_20260709',
    'jugador',
    'es_socio',
    'cuota_social_pago',
    'ux_cuota_social_pago_jugador_periodo',
    'action_sociosCuota_equipo',
    'action_sociosCuota_guardar',
    'action_sociosCuota_informe',
    'db->createCommand',
);

foreach($required as $needle) {
    if(strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL: el migrador no contiene " . $needle . PHP_EOL);
        exit(1);
    }
}

echo "OK migrador web cuotas" . PHP_EOL;
