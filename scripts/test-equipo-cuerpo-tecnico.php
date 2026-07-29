<?php

function failTest($message)
{
	fwrite(STDERR, $message . "\n");
	exit(1);
}

function readProjectFile($relativePath)
{
	$contents = @file_get_contents(__DIR__ . '/../' . $relativePath);
	if($contents === false)
		failTest('No se pudo leer ' . $relativePath);
	return $contents;
}

function requireSnippet($contents, $snippet, $message)
{
	if(strpos($contents, $snippet) === false)
		failTest($message);
}

$migration = readProjectFile('protected/migrations/m260729_000001_add_cuerpo_tecnico_to_equipos.php');
$model = readProjectFile('protected/models/Equipos.php');
$form = readProjectFile('protected/views/equipos/_form.php');
$pdf = readProjectFile('protected/views/equipos/pdf.php');
$excel = readProjectFile('protected/views/equipos/excel.php');

requireSnippet($migration, "columnExists('equipos', 'Tecnico')", 'La migracion debe controlar equipos.Tecnico.');
requireSnippet($migration, "columnExists('equipos', 'AyudanteTecnico')", 'La migracion debe controlar equipos.AyudanteTecnico.');
requireSnippet($migration, "VARCHAR(100) NOT NULL DEFAULT ''", 'Las columnas deben aceptar nombres de 100 caracteres y usar valor vacio.');

requireSnippet($model, "array('Tecnico, AyudanteTecnico', 'length', 'max'=>100)", 'El modelo debe validar ambos nombres.');
requireSnippet($model, "'Tecnico' => 'Técnico'", 'Falta la etiqueta Técnico.');
requireSnippet($model, "'AyudanteTecnico' => 'Ayudante de técnico'", 'Falta la etiqueta Ayudante de técnico.');

requireSnippet($form, "CHtml::activeTextField(\$model, 'Tecnico'", 'El formulario debe editar Técnico.');
requireSnippet($form, "CHtml::activeTextField(\$model, 'AyudanteTecnico'", 'El formulario debe editar Ayudante de técnico.');

requireSnippet($pdf, 'T&eacute;cnico:', 'El PDF debe imprimir siempre el rótulo Técnico.');
requireSnippet($pdf, 'Ayudante de t&eacute;cnico:', 'El PDF debe imprimir siempre el rótulo Ayudante de técnico.');
requireSnippet($pdf, 'CHtml::encode($tecnico)', 'El PDF debe escapar el nombre del técnico.');
requireSnippet($pdf, 'CHtml::encode($ayudanteTecnico)', 'El PDF debe escapar el nombre del ayudante.');

requireSnippet($excel, 'T&eacute;cnico', 'Excel debe imprimir siempre el rótulo Técnico.');
requireSnippet($excel, 'Ayudante de t&eacute;cnico', 'Excel debe imprimir siempre el rótulo Ayudante de técnico.');
requireSnippet($excel, 'CHtml::encode($tecnico)', 'Excel debe escapar el nombre del técnico.');
requireSnippet($excel, 'CHtml::encode($ayudanteTecnico)', 'Excel debe escapar el nombre del ayudante.');

echo "OK: cuerpo técnico disponible en equipo, PDF y Excel.\n";
