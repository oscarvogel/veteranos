<?php

class m260512_000001_add_fecha_nacimiento_to_jugador extends CDbMigration
{
	public function up()
	{
		$table = $this->dbConnection->schema->getTable('jugador');
		if($table !== null && !isset($table->columns['fecha_nacimiento']))
			$this->addColumn('jugador', 'fecha_nacimiento', 'DATE');
	}

	public function down()
	{
		$table = $this->dbConnection->schema->getTable('jugador');
		if($table !== null && isset($table->columns['fecha_nacimiento']))
			$this->dropColumn('jugador', 'fecha_nacimiento');
	}
}
