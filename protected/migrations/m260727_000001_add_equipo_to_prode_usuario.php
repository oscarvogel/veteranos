<?php

/**
 * Agrega el campo idEquipo (FK a equipos, NULL) en prode_usuario para que
 * los participantes puedan identificarse con un equipo y competir en el
 * ranking por equipos.
 *
 * - Columna: idEquipo INT(10) UNSIGNED NULL
 * - Index:   ix_prode_usuario_equipo
 * - FK:      fk_prode_usuario_equipo -> equipos.idEquipo (CASCADE, envuelto en try/catch
 *            como el resto del proyecto, porque algunos servers de hosting compartido
 *            no soportan todos los FKs).
 */
class m260727_000001_add_equipo_to_prode_usuario extends CDbMigration
{
	public function safeUp()
	{
		if (!$this->columnExists('prode_usuario', 'idEquipo')) {
			$this->addColumn('prode_usuario', 'idEquipo', 'INT(10) UNSIGNED NULL AFTER esAdmin');
		}

		if (!$this->indexExists('prode_usuario', 'ix_prode_usuario_equipo')) {
			$this->createIndex('ix_prode_usuario_equipo', 'prode_usuario', 'idEquipo');
		}

		// FK con try/catch (patron del proyecto: m260709_cuotas_sociales,
		// m260704_jugador_documento). Si el server de hosting no soporta el FK
		// queda como warning, la app Yii valida la relacion igual.
		try {
			$this->addForeignKey('fk_prode_usuario_equipo', 'prode_usuario', 'idEquipo', 'equipos', 'idEquipo', 'CASCADE', 'CASCADE');
		} catch (Exception $e) {
			// Instalaciones antiguas pueden no tener metadata compatible;
			// la app valida la relacion via la relacion BELONGS_TO del modelo.
		}
	}

	public function safeDown()
	{
		try {
			$this->dropForeignKey('fk_prode_usuario_equipo', 'prode_usuario');
		} catch (Exception $e) {
		}

		if ($this->indexExists('prode_usuario', 'ix_prode_usuario_equipo')) {
			$this->dropIndex('ix_prode_usuario_equipo', 'prode_usuario');
		}

		if ($this->columnExists('prode_usuario', 'idEquipo')) {
			$this->dropColumn('prode_usuario', 'idEquipo');
		}
	}

	private function columnExists($table, $column)
	{
		$tableSchema = $this->dbConnection->schema->getTable($table);
		return $tableSchema !== null && isset($tableSchema->columns[$column]);
	}

	private function indexExists($tableName, $indexName)
	{
		$row = $this->dbConnection->createCommand(
			'SHOW INDEX FROM ' . $tableName . ' WHERE Key_name = :indexName'
		)->queryRow(true, array(':indexName' => $indexName));

		return $row !== false;
	}
}
