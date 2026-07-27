<?php

class m260709_000001_add_cuotas_sociales extends CDbMigration
{
	private $operations = array(
		'action_sociosCuota_equipo' => 'Gestionar cuotas sociales por equipo',
		'action_sociosCuota_guardar' => 'Guardar pagos de cuotas sociales',
		'action_sociosCuota_informe' => 'Consultar informe de cuotas sociales',
	);

	public function safeUp()
	{
		if(!$this->columnExists('jugador', 'es_socio'))
			$this->addColumn('jugador', 'es_socio', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER dec_jurada');

		if($this->dbConnection->schema->getTable('cuota_social_pago') === null) {
			$this->createTable('cuota_social_pago', array(
				'idPago' => 'pk',
				'idJugador' => 'INT(10) UNSIGNED NOT NULL',
				'periodo' => 'CHAR(7) NOT NULL',
				'fecha_pago' => 'DATE NOT NULL',
				'idUsuario' => 'INT(10) NULL',
				'observacion' => 'VARCHAR(250) NULL',
				'created_at' => 'DATETIME NOT NULL',
				'updated_at' => 'DATETIME NOT NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('ux_cuota_social_pago_jugador_periodo', 'cuota_social_pago', 'idJugador,periodo', true);
			$this->createIndex('ix_cuota_social_pago_periodo', 'cuota_social_pago', 'periodo');
			$this->createIndex('ix_cuota_social_pago_usuario', 'cuota_social_pago', 'idUsuario');

			try {
				$this->addForeignKey('fk_cuota_social_pago_jugador', 'cuota_social_pago', 'idJugador', 'jugador', 'idJugador', 'CASCADE', 'CASCADE');
			} catch(Exception $e) {
				// Instalaciones antiguas pueden no tener metadata compatible; la app valida la relacion.
			}
		}

		foreach($this->operations as $name=>$description)
			$this->insertAuthItemIfMissing($name, 0, $description);

		foreach(array('admin', 'administrador', 'cajero') as $role) {
			if($this->authItemExists($role)) {
				foreach(array_keys($this->operations) as $operation)
					$this->insertAuthChildIfMissing($role, $operation);
			}
		}
	}

	public function safeDown()
	{
		foreach(array('admin', 'administrador', 'cajero') as $parent) {
			if($this->authItemExists($parent)) {
				foreach(array_keys($this->operations) as $child) {
					$this->delete('cruge_authitemchild', 'parent = :parent AND child = :child', array(
						':parent'=>$parent,
						':child'=>$child,
					));
				}
			}
		}

		foreach(array_keys($this->operations) as $name)
			$this->delete('cruge_authitem', 'name = :name', array(':name'=>$name));

		if($this->dbConnection->schema->getTable('cuota_social_pago') !== null) {
			try {
				$this->dropForeignKey('fk_cuota_social_pago_jugador', 'cuota_social_pago');
			} catch(Exception $e) {
			}
			$this->dropTable('cuota_social_pago');
		}

		if($this->columnExists('jugador', 'es_socio'))
			$this->dropColumn('jugador', 'es_socio');
	}

	private function columnExists($table, $column)
	{
		$tableSchema = $this->dbConnection->schema->getTable($table);
		return $tableSchema !== null && isset($tableSchema->columns[$column]);
	}

	private function insertAuthItemIfMissing($name, $type, $description)
	{
		if(!$this->authItemExists($name)) {
			$this->insert('cruge_authitem', array(
				'name'=>$name,
				'type'=>$type,
				'description'=>$description,
				'bizrule'=>null,
				'data'=>null,
			));
		}
	}

	private function insertAuthChildIfMissing($parent, $child)
	{
		if(!$this->authItemExists($parent) || !$this->authItemExists($child))
			return;

		$count = (int)$this->dbConnection->createCommand(
			'SELECT COUNT(*) FROM cruge_authitemchild WHERE parent = :parent AND child = :child'
		)->queryScalar(array(':parent'=>$parent, ':child'=>$child));
		if($count === 0)
			$this->insert('cruge_authitemchild', array('parent'=>$parent, 'child'=>$child));
	}

	private function authItemExists($name)
	{
		$count = (int)$this->dbConnection->createCommand('SELECT COUNT(*) FROM cruge_authitem WHERE name = :name')
			->queryScalar(array(':name'=>$name));
		return $count > 0;
	}
}
