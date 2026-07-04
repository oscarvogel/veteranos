<?php

class m260702_000001_add_recibo_fields_to_ingresos extends CDbMigration
{
	public function up()
	{
		$this->execute("ALTER TABLE ingresos MODIFY Fecha DATE NOT NULL");

		if(!$this->columnExists('ingresos', 'NumeroRecibo'))
			$this->addColumn('ingresos', 'NumeroRecibo', 'INT NULL AFTER idIngreso');
		if(!$this->columnExists('ingresos', 'Estado'))
			$this->addColumn('ingresos', 'Estado', "VARCHAR(20) NOT NULL DEFAULT 'VIGENTE' AFTER NumeroRecibo");
		if(!$this->columnExists('ingresos', 'idUsuario'))
			$this->addColumn('ingresos', 'idUsuario', 'INT NULL AFTER Estado');
		if(!$this->columnExists('ingresos', 'FechaAlta'))
			$this->addColumn('ingresos', 'FechaAlta', 'DATETIME NULL AFTER idUsuario');
		if(!$this->columnExists('ingresos', 'FechaAnulacion'))
			$this->addColumn('ingresos', 'FechaAnulacion', 'DATETIME NULL AFTER FechaAlta');
		if(!$this->columnExists('ingresos', 'MotivoAnulacion'))
			$this->addColumn('ingresos', 'MotivoAnulacion', 'VARCHAR(250) NULL AFTER FechaAnulacion');

		$this->execute("UPDATE ingresos
			SET NumeroRecibo = idIngreso,
				Estado = 'VIGENTE',
				FechaAlta = CONCAT(Fecha, ' ', COALESCE(Hora, '00:00:00'))
			WHERE NumeroRecibo IS NULL");

		if(!$this->indexExists('ingresos', 'ux_ingresos_numero_recibo'))
			$this->createIndex('ux_ingresos_numero_recibo', 'ingresos', 'NumeroRecibo', true);
		if(!$this->indexExists('ingresos', 'ix_ingresos_fecha_estado'))
			$this->createIndex('ix_ingresos_fecha_estado', 'ingresos', 'Fecha, Estado');
		if(!$this->indexExists('ingresos', 'ix_ingresos_usuario_fecha'))
			$this->createIndex('ix_ingresos_usuario_fecha', 'ingresos', 'idUsuario, Fecha');
	}

	public function down()
	{
		if($this->indexExists('ingresos', 'ix_ingresos_usuario_fecha'))
			$this->dropIndex('ix_ingresos_usuario_fecha', 'ingresos');
		if($this->indexExists('ingresos', 'ix_ingresos_fecha_estado'))
			$this->dropIndex('ix_ingresos_fecha_estado', 'ingresos');
		if($this->indexExists('ingresos', 'ux_ingresos_numero_recibo'))
			$this->dropIndex('ux_ingresos_numero_recibo', 'ingresos');

		foreach(array('MotivoAnulacion', 'FechaAnulacion', 'FechaAlta', 'idUsuario', 'Estado', 'NumeroRecibo') as $column) {
			if($this->columnExists('ingresos', $column))
				$this->dropColumn('ingresos', $column);
		}
	}

	private function columnExists($table, $column)
	{
		$tableSchema = $this->dbConnection->schema->getTable($table);
		return $tableSchema !== null && isset($tableSchema->columns[$column]);
	}

	private function indexExists($table, $index)
	{
		$row = $this->dbConnection->createCommand(
			"SELECT COUNT(*) FROM information_schema.statistics
			WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index"
		)->queryScalar(array(':table'=>$table, ':index'=>$index));
		return (int)$row > 0;
	}
}
