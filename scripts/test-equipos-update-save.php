<?php

$controllerPath = __DIR__ . '/../protected/controllers/EquiposController.php';
$formPath = __DIR__ . '/../protected/views/equipos/_form.php';
$source = file_get_contents($controllerPath);
$formSource = file_get_contents($formPath);

if ($source === false) {
    fwrite(STDERR, "No se pudo leer EquiposController.php\n");
    exit(1);
}
if ($formSource === false) {
    fwrite(STDERR, "No se pudo leer protected/views/equipos/_form.php\n");
    exit(1);
}

$actionStart = strpos($source, 'public function actionUpdate($id)');
if ($actionStart === false) {
    fwrite(STDERR, "No se encontro actionUpdate\n");
    exit(1);
}

$actionEnd = strpos($source, '/**', $actionStart + 1);
if ($actionEnd === false) {
    fwrite(STDERR, "No se encontro el fin de actionUpdate\n");
    exit(1);
}

$actionSource = substr($source, $actionStart, $actionEnd - $actionStart);

$requiredSnippets = array(
    '$model->save()' => 'guarda el equipo desde actionUpdate',
    'hayCambiosJugadoresEquipo($_POST)' => 'evita validar jugadores cuando no se editaron',
    'MultiModelForm::save($jugador, $validatedMembers, $deleteMembers, $masterValues)' => 'mantiene el guardado de jugadores cuando corresponde',
    '$this->redirect(array(\'view\',\'id\'=>$model->idEquipo));' => 'redirige despues de guardar correctamente',
);

foreach ($requiredSnippets as $snippet => $description) {
    if (strpos($actionSource, $snippet) === false) {
        fwrite(STDERR, "actionUpdate no {$description}.\n");
        exit(1);
    }
}

$modelSavePos = strpos($actionSource, '$model->save()');
$multiSavePos = strpos($actionSource, 'MultiModelForm::save');
if ($modelSavePos === false || $multiSavePos === false || $modelSavePos > $multiSavePos) {
    fwrite(STDERR, "actionUpdate debe guardar Equipos antes de validar o guardar jugadores.\n");
    exit(1);
}

$helperSnippets = array(
    'private function hayCambiosJugadoresEquipo($post)' => 'define helper para detectar cambios de jugadores',
    "\$jugadorPost['n__']" => 'detecta jugadores nuevos',
    "\$jugadorPost['u__']" => 'detecta cambios en jugadores existentes',
    "\$jugadorPost['pk__']" => 'detecta borrados de jugadores',
    "Jugador::model()->findByPk(\$idJugador)" => 'compara contra la base antes de validar jugadores',
);

foreach ($helperSnippets as $snippet => $description) {
    if (strpos($source, $snippet) === false) {
        fwrite(STDERR, "EquiposController no {$description}.\n");
        exit(1);
    }
}

if (strpos($formSource, "'enableAjaxValidation'=>false") === false) {
    fwrite(STDERR, "equipos _form debe desactivar validacion AJAX para no bloquear por dummy de MultiModelForm.\n");
    exit(1);
}

echo "OK: actionUpdate guarda datos de equipo sin bloquearse por jugadores sin fecha.\n";
