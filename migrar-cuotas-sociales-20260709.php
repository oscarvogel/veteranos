<?php

define('MIGRADOR_CUOTAS_CLAVE', 'MIGRAR_CUOTAS_SOCIALES_20260709');

header('Content-Type: text/html; charset=utf-8');

function h($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function outLine($message, $class = '')
{
	echo '<li' . ($class !== '' ? ' class="' . h($class) . '"' : '') . '>' . h($message) . '</li>' . "\n";
	@ob_flush();
	@flush();
}

echo '<!doctype html><html><head><meta charset="utf-8"><title>Migrar cuotas sociales</title>';
echo '<style>body{font-family:Arial,sans-serif;margin:24px;line-height:1.4} .ok{color:#237804}.skip{color:#666}.err{color:#a8071a} code{background:#f5f5f5;padding:2px 4px}</style>';
echo '</head><body><h1>Migrar cuotas sociales</h1>';

if(!isset($_GET['clave']) || $_GET['clave'] !== MIGRADOR_CUOTAS_CLAVE) {
	echo '<p>Para ejecutar la migracion abra esta URL agregando:</p>';
	echo '<p><code>?clave=' . h(MIGRADOR_CUOTAS_CLAVE) . '</code></p>';
	echo '<p>Ejemplo: <code>migrar-cuotas-sociales-20260709.php?clave=' . h(MIGRADOR_CUOTAS_CLAVE) . '</code></p>';
	echo '<p>Despues de ejecutarlo correctamente, borre este archivo del hosting.</p>';
	echo '</body></html>';
	exit;
}

echo '<ul>';

	try {
	require_once dirname(__FILE__) . '/yii/framework/yii.php';
	Yii::createWebApplication(dirname(__FILE__) . '/protected/config/main.php');
	$db = Yii::app()->db;
	$db->active = true;
	outLine('Conexion a base de datos OK.', 'ok');

	if(!columnExists($db, 'jugador', 'es_socio')) {
		$db->createCommand('ALTER TABLE jugador ADD COLUMN es_socio TINYINT(1) NOT NULL DEFAULT 0 AFTER dec_jurada')->execute();
		outLine('Columna jugador.es_socio creada.', 'ok');
	} else {
		outLine('Columna jugador.es_socio ya existia.', 'skip');
	}

	if($db->schema->getTable('cuota_social_pago') === null) {
		$db->createCommand("CREATE TABLE cuota_social_pago (
			idPago INT NOT NULL AUTO_INCREMENT,
			idJugador INT(10) UNSIGNED NOT NULL,
			periodo CHAR(7) NOT NULL,
			fecha_pago DATE NOT NULL,
			idUsuario INT(10) NULL,
			observacion VARCHAR(250) NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY (idPago),
			UNIQUE KEY ux_cuota_social_pago_jugador_periodo (idJugador, periodo),
			KEY ix_cuota_social_pago_periodo (periodo),
			KEY ix_cuota_social_pago_usuario (idUsuario)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();
		outLine('Tabla cuota_social_pago creada.', 'ok');

		try {
			$db->createCommand('ALTER TABLE cuota_social_pago ADD CONSTRAINT fk_cuota_social_pago_jugador FOREIGN KEY (idJugador) REFERENCES jugador(idJugador) ON DELETE CASCADE ON UPDATE CASCADE')->execute();
			outLine('Foreign key fk_cuota_social_pago_jugador creada.', 'ok');
		} catch(Exception $e) {
			outLine('No se pudo crear foreign key; la app funciona igual. Detalle: ' . $e->getMessage(), 'skip');
		}
	} else {
		outLine('Tabla cuota_social_pago ya existia.', 'skip');
		ensureIndex($db, 'cuota_social_pago', 'ux_cuota_social_pago_jugador_periodo', 'CREATE UNIQUE INDEX ux_cuota_social_pago_jugador_periodo ON cuota_social_pago (idJugador, periodo)');
		ensureIndex($db, 'cuota_social_pago', 'ix_cuota_social_pago_periodo', 'CREATE INDEX ix_cuota_social_pago_periodo ON cuota_social_pago (periodo)');
		ensureIndex($db, 'cuota_social_pago', 'ix_cuota_social_pago_usuario', 'CREATE INDEX ix_cuota_social_pago_usuario ON cuota_social_pago (idUsuario)');
	}

	insertAuthItemIfMissing($db, 'action_sociosCuota_equipo', 0, 'Gestionar cuotas sociales por equipo');
	insertAuthItemIfMissing($db, 'action_sociosCuota_guardar', 0, 'Guardar pagos de cuotas sociales');
	insertAuthItemIfMissing($db, 'action_sociosCuota_informe', 0, 'Consultar informe de cuotas sociales');

	foreach(array('admin', 'administrador', 'cajero') as $role) {
		foreach(array('action_sociosCuota_equipo', 'action_sociosCuota_guardar', 'action_sociosCuota_informe') as $operation)
			insertAuthChildIfMissing($db, $role, $operation);
	}

	outLine('Migracion finalizada correctamente. Borre este archivo del hosting ahora.', 'ok');
} catch(Exception $e) {
	outLine('ERROR: ' . $e->getMessage(), 'err');
	echo '</ul><p>Revise el error antes de volver a ejecutar.</p></body></html>';
	exit(1);
}

echo '</ul></body></html>';

function columnExists($db, $table, $column)
{
	$tableSchema = $db->schema->getTable($table);
	return $tableSchema !== null && isset($tableSchema->columns[$column]);
}

function indexExists($db, $table, $index)
{
	$count = $db->createCommand(
		"SELECT COUNT(*) FROM information_schema.statistics
		WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index"
	)->queryScalar(array(':table'=>$table, ':index'=>$index));
	return (int)$count > 0;
}

function ensureIndex($db, $table, $index, $sql)
{
	if(indexExists($db, $table, $index)) {
		outLine('Indice ' . $index . ' ya existia.', 'skip');
		return;
	}

	$db->createCommand($sql)->execute();
	outLine('Indice ' . $index . ' creado.', 'ok');
}

function authItemExists($db, $name)
{
	$count = $db->createCommand('SELECT COUNT(*) FROM cruge_authitem WHERE name = :name')
		->queryScalar(array(':name'=>$name));
	return (int)$count > 0;
}

function insertAuthItemIfMissing($db, $name, $type, $description)
{
	if(authItemExists($db, $name)) {
		outLine('Permiso ' . $name . ' ya existia.', 'skip');
		return;
	}

	$db->createCommand()->insert('cruge_authitem', array(
		'name'=>$name,
		'type'=>$type,
		'description'=>$description,
		'bizrule'=>null,
		'data'=>null,
	));
	outLine('Permiso ' . $name . ' creado.', 'ok');
}

function insertAuthChildIfMissing($db, $parent, $child)
{
	if(!authItemExists($db, $parent) || !authItemExists($db, $child)) {
		outLine('Asignacion ' . $parent . ' -> ' . $child . ' omitida porque falta rol/permisos.', 'skip');
		return;
	}

	$count = $db->createCommand('SELECT COUNT(*) FROM cruge_authitemchild WHERE parent = :parent AND child = :child')
		->queryScalar(array(':parent'=>$parent, ':child'=>$child));
	if((int)$count > 0) {
		outLine('Asignacion ' . $parent . ' -> ' . $child . ' ya existia.', 'skip');
		return;
	}

	$db->createCommand()->insert('cruge_authitemchild', array('parent'=>$parent, 'child'=>$child));
	outLine('Asignacion ' . $parent . ' -> ' . $child . ' creada.', 'ok');
}
