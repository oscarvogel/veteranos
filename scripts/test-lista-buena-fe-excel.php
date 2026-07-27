<?php
$viewPath = __DIR__ . '/../protected/views/equipos/excel.php';
$view = file_get_contents($viewPath);

if ($view === false) {
	fwrite(STDERR, "No se pudo leer protected/views/equipos/excel.php\n");
	exit(1);
}

if (strpos($view, '<th>Fecha de nacimiento</th>') === false) {
	fwrite(STDERR, "El export Excel debe mostrar la columna Fecha de nacimiento.\n");
	exit(1);
}

if (strpos($view, 'listaBuenaFeExcelFechaNacimiento($jugador->fecha_nacimiento)') === false) {
	fwrite(STDERR, "El export Excel debe usar jugador->fecha_nacimiento.\n");
	exit(1);
}

if (strpos($view, '<th>Clase</th>') !== false || strpos($view, '$jugador->Clase') !== false) {
	fwrite(STDERR, "El export Excel no debe exportar Clase en lugar de fecha de nacimiento.\n");
	exit(1);
}

echo "OK lista buena fe Excel exporta fecha de nacimiento\n";
