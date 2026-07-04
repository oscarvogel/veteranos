<?php

require_once dirname(__FILE__) . '/../yii/framework/yii.php';

Yii::createConsoleApplication(dirname(__FILE__) . '/../protected/config/console.php');
require_once dirname(__FILE__) . '/../protected/components/Controller.php';
require_once dirname(__FILE__) . '/../protected/controllers/IngresosController.php';

function assertTrue($condition, $message)
{
    if(!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

$controller = new IngresosController('ingresos');
foreach(array('actionReciboPdf', 'actionArqueoCaja', 'actionAnular', 'actionResumenMensual') as $method) {
    assertTrue(method_exists($controller, $method), 'IngresosController debe implementar ' . $method);
}

foreach(array('reciboPdf.php', 'arqueoCaja.php', 'anular.php', 'resumenMensual.php') as $view) {
    assertTrue(is_file(dirname(__FILE__) . '/../protected/views/ingresos/' . $view), 'Debe existir protected/views/ingresos/' . $view);
}
assertTrue(is_file(dirname(__FILE__) . '/../media/firmas/firma-recibo-mini.jpg'), 'Debe existir la firma del recibo');

$reciboPdf = file_get_contents(dirname(__FILE__) . '/../protected/views/ingresos/reciboPdf.php');
assertTrue(strpos($reciboPdf, 'firma-recibo-mini.jpg') !== false, 'El PDF del recibo debe incluir la firma miniatura');
assertTrue(strpos($reciboPdf, 'width="45"') !== false, 'El PDF del recibo debe fijar ancho reducido para la firma');
assertTrue(strpos($reciboPdf, 'Tesoreria Asociacion') !== false, 'El PDF del recibo debe mostrar Tesoreria Asociacion');
assertTrue(strpos($reciboPdf, 'Usuario->username') === false, 'El PDF del recibo no debe mostrar el usuario tecnico');

$layout = file_get_contents(dirname(__FILE__) . '/../themes/classic/views/layouts/main.php');
assertTrue(strpos($layout, 'ingresos/arqueoCaja') !== false, 'El menu principal debe tener acceso al arqueo de caja');
assertTrue(strpos($layout, 'ingresos/resumenMensual') !== false, 'El menu principal debe tener acceso al resumen mensual');
assertTrue(strpos($layout, 'Registrar pago') !== false, 'El menu principal debe tener acceso a registrar pago');

$adminView = file_get_contents(dirname(__FILE__) . '/../protected/views/ingresos/admin.php');
assertTrue(strpos($adminView, 'recibo-pdf.png') !== false, 'La grilla de recibos debe mostrar imagen para PDF');
assertTrue(strpos($adminView, 'recibo-whatsapp.png') !== false, 'La grilla de recibos debe mostrar imagen para WhatsApp');
assertTrue(strpos($adminView, 'recibo-anular.png') !== false, 'La grilla de recibos debe mostrar imagen para Anular');
assertTrue(is_file(dirname(__FILE__) . '/../media/iconos/recibo-pdf.png'), 'Debe existir el icono PNG para PDF');
assertTrue(is_file(dirname(__FILE__) . '/../media/iconos/recibo-whatsapp.png'), 'Debe existir el icono PNG para WhatsApp');
assertTrue(is_file(dirname(__FILE__) . '/../media/iconos/recibo-anular.png'), 'Debe existir el icono PNG para Anular');

$arqueoView = file_get_contents(dirname(__FILE__) . '/../protected/views/ingresos/arqueoCaja.php');
assertTrue(strpos($arqueoView, 'recibo-pdf.png') !== false, 'La grilla de arqueo debe mostrar imagen para PDF');

echo "OK recibos ui" . PHP_EOL;
