<?php

function assertSnippet($path, $snippet, $message) {
    $source = file_get_contents($path);
    if ($source === false) {
        fwrite(STDERR, "No se pudo leer {$path}\n");
        exit(1);
    }

    if (strpos($source, $snippet) === false) {
        fwrite(STDERR, $message . "\nFalta: " . $snippet . "\nArchivo: " . $path . "\n");
        exit(1);
    }
}

$root = dirname(__DIR__);
$controller = $root . '/protected/controllers/JugadorController.php';
$view = $root . '/protected/views/jugador/importarListaBuenaFe.php';
$component = $root . '/protected/components/ListaBuenaFeImporter.php';
$admin = $root . '/protected/views/jugador/admin.php';

assertSnippet($controller, "'importarListaBuenaFe'", 'JugadorController debe permitir la accion Yii de importacion');
assertSnippet($controller, 'public function actionImportarListaBuenaFe()', 'JugadorController debe exponer actionImportarListaBuenaFe');
assertSnippet($controller, 'CUploadedFile::getInstanceByName', 'La accion debe recibir un archivo subido por formulario Yii');
assertSnippet($controller, 'ListaBuenaFeImporter', 'La accion debe delegar la logica de importacion en un componente');

assertSnippet($view, "enctype'=>'multipart/form-data'", 'El formulario Yii debe subir archivos con multipart/form-data');
assertSnippet($view, "CHtml::dropDownList('idEquipo'", 'El formulario debe tener combo de equipos');
assertSnippet($view, "CHtml::fileField('archivo'", 'El formulario debe tener input de archivo');
assertSnippet($view, 'DNI no encontrados', 'La vista debe informar DNIs no encontrados');
assertSnippet($view, 'Fechas invalidas', 'La vista debe informar fechas invalidas');

assertSnippet($component, 'class ListaBuenaFeImporter', 'Debe existir el componente importador');
assertSnippet($component, 'buscarJugadorPorDni', 'El importador debe buscar jugadores por DNI');
assertSnippet($component, '->update(', 'El importador debe actualizar registros');
assertSnippet($component, "'jugador'", 'El importador debe actualizar la tabla jugador');
assertSnippet($component, 'fecha_nacimiento', 'El importador debe completar fecha_nacimiento cuando falte');
assertSnippet($component, 'leerXlsx', 'El importador debe aceptar XLSX ademas de CSV');

assertSnippet($admin, "array('label'=>'Importar lista buena fe'", 'El admin de jugadores debe enlazar el formulario de importacion');

echo "OK: importacion Yii de lista buena fe tiene accion, formulario y componente.\n";
