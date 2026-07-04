<?php

class m260704_000001_add_jugador_documento_legajo extends CDbMigration
{
	private $operations = array(
		'action_jugador_legajo' => 'Ver legajo digital de jugadores',
		'action_jugador_subirDocumento' => 'Subir documentos al legajo digital',
		'action_jugador_descargarDocumento' => 'Descargar documentos del legajo digital',
		'action_jugador_eliminarDocumento' => 'Eliminar documentos del legajo digital',
	);

	public function safeUp()
	{
		if($this->dbConnection->schema->getTable('jugador_documento') === null) {
			$this->createTable('jugador_documento', array(
				'idDocumento' => 'pk',
				'idJugador' => 'INT(10) UNSIGNED NOT NULL',
				'tipo' => 'VARCHAR(40) NOT NULL',
				'titulo' => 'VARCHAR(120) NULL',
				'archivo_original' => 'VARCHAR(255) NOT NULL',
				'archivo_guardado' => 'VARCHAR(255) NOT NULL',
				'mime_type' => 'VARCHAR(100) NOT NULL',
				'extension' => 'VARCHAR(10) NOT NULL',
				'tamano_bytes' => 'INT UNSIGNED NOT NULL',
				'observacion' => 'VARCHAR(500) NULL',
				'idUsuario' => 'INT(10) NULL',
				'created_at' => 'DATETIME NOT NULL',
				'updated_at' => 'DATETIME NOT NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('idx_jugador_documento_jugador', 'jugador_documento', 'idJugador');
			$this->createIndex('idx_jugador_documento_tipo', 'jugador_documento', 'tipo');
			$this->createIndex('idx_jugador_documento_jugador_tipo', 'jugador_documento', 'idJugador,tipo');

			try {
				$this->addForeignKey('fk_jugador_documento_jugador', 'jugador_documento', 'idJugador', 'jugador', 'idJugador', 'CASCADE', 'CASCADE');
			} catch(Exception $e) {
				// Algunas instalaciones antiguas pueden no tener metadata compatible; la relacion queda validada por la app.
			}
		}

		foreach($this->operations as $name=>$description) {
			$this->insertAuthItemIfMissing($name, 0, $description);
		}

		foreach(array('admin', 'administrador', 'delegado') as $role) {
			if($this->authItemExists($role)) {
				foreach(array_keys($this->operations) as $operation) {
					$this->insertAuthChildIfMissing($role, $operation);
				}
			}
		}
	}

	public function safeDown()
	{
		foreach(array('admin', 'administrador', 'delegado') as $parent) {
			if($this->authItemExists($parent)) {
				foreach(array_keys($this->operations) as $child) {
					$this->delete('cruge_authitemchild', 'parent = :parent AND child = :child', array(
						':parent'=>$parent,
						':child'=>$child,
					));
				}
			}
		}

		foreach(array_keys($this->operations) as $name) {
			$this->delete('cruge_authitem', 'name = :name', array(':name'=>$name));
		}

		if($this->dbConnection->schema->getTable('jugador_documento') !== null) {
			try {
				$this->dropForeignKey('fk_jugador_documento_jugador', 'jugador_documento');
			} catch(Exception $e) {
			}
			$this->dropTable('jugador_documento');
		}
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
