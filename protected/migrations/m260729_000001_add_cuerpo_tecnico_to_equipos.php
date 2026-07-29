<?php

class m260729_000001_add_cuerpo_tecnico_to_equipos extends CDbMigration
{
	public function safeUp()
	{
		if(!$this->columnExists('equipos', 'Tecnico'))
			$this->addColumn('equipos', 'Tecnico', "VARCHAR(100) NOT NULL DEFAULT '' AFTER DelegadoSuplente");

		if(!$this->columnExists('equipos', 'AyudanteTecnico'))
			$this->addColumn('equipos', 'AyudanteTecnico', "VARCHAR(100) NOT NULL DEFAULT '' AFTER Tecnico");
	}

	public function safeDown()
	{
		if($this->columnExists('equipos', 'AyudanteTecnico'))
			$this->dropColumn('equipos', 'AyudanteTecnico');

		if($this->columnExists('equipos', 'Tecnico'))
			$this->dropColumn('equipos', 'Tecnico');
	}

	private function columnExists($table, $column)
	{
		$tableSchema = $this->dbConnection->schema->getTable($table);
		return $tableSchema !== null && isset($tableSchema->columns[$column]);
	}
}
