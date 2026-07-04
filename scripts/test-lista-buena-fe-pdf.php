<?php

$controllerPath = __DIR__ . '/../protected/controllers/EquiposController.php';
$viewPath = __DIR__ . '/../protected/views/equipos/pdf.php';
$listaViewPath = __DIR__ . '/../protected/views/equipos/ListaBuenaFe.php';
$logoPath = __DIR__ . '/../imagenes/encabezado-lista-buena-fe.png';
$arbitrosPath = __DIR__ . '/../media/arbitros-2026.pdf';
$mpdfPath = __DIR__ . '/../protected/vendors/mpdf/mpdf.php';
$mpdfFunctionsPath = __DIR__ . '/../protected/vendors/mpdf/includes/functions.php';

$controller = file_get_contents($controllerPath);
if ($controller === false) {
    fwrite(STDERR, "No se pudo leer EquiposController.php\n");
    exit(1);
}

if (strpos($controller, "isset(\$_POST['btnLista'])") === false
    || strpos($controller, "renderPartial('pdf'") === false
    || strpos($controller, 'E_DEPRECATED') === false
    || strpos($controller, "\$mPDF1->Output(\$filename, 'D')") === false) {
    fwrite(STDERR, "El boton Lista debe descargar el PDF usando la vista pdf.\n");
    exit(1);
}

if (!file_exists($logoPath) || filesize($logoPath) < 1000) {
    fwrite(STDERR, "Falta el encabezado grafico de lista de buena fe.\n");
    exit(1);
}

if (!file_exists($arbitrosPath) || filesize($arbitrosPath) < 100000) {
    fwrite(STDERR, "Falta el PDF estatico de arbitros/reverso para descarga manual.\n");
    exit(1);
}

$listaView = file_get_contents($listaViewPath);
if ($listaView === false) {
    fwrite(STDERR, "No se pudo leer protected/views/equipos/ListaBuenaFe.php\n");
    exit(1);
}

if (strpos($listaView, 'btnListaBuenaFe') === false
    || strpos($listaView, 'btnReversoListaBuenaFe') === false
    || strpos($listaView, '/media/arbitros-2026.pdf') === false
    || strpos($listaView, 'icon-download-alt') === false) {
    fwrite(STDERR, "La vista ListaBuenaFe debe mostrar el reverso como descarga manual con icono.\n");
    exit(1);
}

if (strpos($listaView, 'descargarArbitrosListaBuenaFe') !== false
    || strpos($listaView, 'link.click()') !== false
    || strpos($listaView, "link.download = 'Arbitros 2026.pdf'") !== false) {
    fwrite(STDERR, "El boton Lista no debe disparar automaticamente el PDF de arbitros/reverso.\n");
    exit(1);
}

$view = file_get_contents($viewPath);
if ($view === false) {
    fwrite(STDERR, "No se pudo leer protected/views/equipos/pdf.php\n");
    exit(1);
}

if (strpos($view, 'imagenes/logo.png') !== false || strpos($view, 'logo.png') !== false) {
    fwrite(STDERR, "La vista PDF no debe usar otro logo que el encabezado provisto.\n");
    exit(1);
}

$requiredSnippets = array(
    'lista-buena-fe-unidos.jpg' => 'usa el logo Unidos del encabezado enviado',
    'lista-buena-fe-asociacion.jpg' => 'usa el logo de asociacion del encabezado enviado',
    'listaBuenaFeLogo' => 'escribe los logos en runtime para mPDF',
    'S U P E R&nbsp;&nbsp;V E T E R A N O S' => 'distingue categoria Super',
    'S E N I O R' => 'distingue categoria Senior',
    'Apellido y Nombres' => 'incluye columna de nombre oficial',
    'Fecha Nac.' => 'incluye columna de fecha de nacimiento',
    'listaBuenaFeFechaNacimiento' => 'formatea fecha de nacimiento',
    'N&deg; Documento' => 'incluye columna de documento',
    'Delegado Titular' => 'incluye datos de delegado',
    'margin-top: 5mm' => 'separa el pie de firmas de la tabla',
    'Cambios:' => 'incluye seccion de cambios',
);

foreach ($requiredSnippets as $snippet => $description) {
    if (strpos($view, $snippet) === false) {
        fwrite(STDERR, "La vista PDF no {$description}.\n");
        exit(1);
    }
}

if (strpos($view, 'data:image') !== false) {
    fwrite(STDERR, "La vista PDF no debe usar data URI para imagenes en mPDF.\n");
    exit(1);
}

if (strpos($view, '<th class="class">Clase</th>') !== false || strpos($view, '$jugador->Clase') !== false) {
    fwrite(STDERR, "La vista PDF no debe usar Clase como columna de lista.\n");
    exit(1);
}

if (strpos($view, 'Camiseta') !== false
    || strpos($view, '$camiseta') !== false
    || strpos($view, 'Cancha') !== false
    || strpos($view, '$cancha') !== false) {
    fwrite(STDERR, "La lista de buena fe PDF no debe imprimir cancha ni camisetas.\n");
    exit(1);
}

$mpdf = file_get_contents($mpdfPath);
if ($mpdf === false) {
    fwrite(STDERR, "No se pudo leer protected/vendors/mpdf/mpdf.php\n");
    exit(1);
}

if (strpos($mpdf, "case 'A4': default:") !== false
    || strpos($mpdf, 'function mPDF(') !== false
    || strpos($mpdf, 'if (!is_array($attr)) { $attr = array(); }') === false
    || strpos($mpdf, "if (\$size === null || \$size === '') { return 0; }") === false
    || strpos($mpdf, 'is_array($this->divbuffer) && count($this->divbuffer)') === false
    || strpos($mpdf, '$owner_RC4_key[$2]') !== false
    || strpos($mpdf, '$hs[$2].$hs[$2]') !== false
    || preg_match('/\$[A-Za-z_][A-Za-z0-9_]*(?:->[A-Za-z_][A-Za-z0-9_]*|\[[^\]\r\n]+\])*\{[^{}\r\n]+\}/', $mpdf)) {
    fwrite(STDERR, "mPDF conserva sintaxis incompatible con PHP actual.\n");
    exit(1);
}

$mpdfFunctions = file_get_contents($mpdfFunctionsPath);
if ($mpdfFunctions === false) {
    fwrite(STDERR, "No se pudo leer protected/vendors/mpdf/includes/functions.php\n");
    exit(1);
}

if (strpos($mpdfFunctions, 'preg_replace_callback') === false
    || preg_match('/preg_replace\([^,\r\n]+\/[a-zA-Z]*e[a-zA-Z]*[\'"]/', $mpdfFunctions)) {
    fwrite(STDERR, "mPDF conserva preg_replace con modificador /e.\n");
    exit(1);
}

echo "OK: Lista de buena fe genera PDF dinamico con encabezado y categorias.\n";
