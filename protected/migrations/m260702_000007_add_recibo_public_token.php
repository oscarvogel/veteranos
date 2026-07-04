<?php

class m260702_000007_add_recibo_public_token extends CDbMigration
{
	public function up()
	{
		$table = $this->dbConnection->schema->getTable('ingresos');
		if(!isset($table->columns['ReciboToken'])) {
			$this->addColumn('ingresos', 'ReciboToken', 'VARCHAR(64) NULL AFTER MotivoAnulacion');
		}
		$table = $this->dbConnection->schema->getTable('ingresos', true);
		if(isset($table->columns['ReciboToken']) && !$this->indexExists('ingresos', 'ux_ingresos_recibo_token')) {
			$this->createIndex('ux_ingresos_recibo_token', 'ingresos', 'ReciboToken', true);
		}
	}

	public function down()
	{
		if($this->indexExists('ingresos', 'ux_ingresos_recibo_token')) {
			$this->dropIndex('ux_ingresos_recibo_token', 'ingresos');
		}
		$table = $this->dbConnection->schema->getTable('ingresos', true);
		if(isset($table->columns['ReciboToken'])) {
			$this->dropColumn('ingresos', 'ReciboToken');
		}
	}

	private function indexExists($tableName, $indexName)
	{
		$row = $this->dbConnection->createCommand(
			'SHOW INDEX FROM ' . $tableName . ' WHERE Key_name = :indexName'
		)->queryRow(true, array(':indexName'=>$indexName));

		return $row !== false;
	}
}
