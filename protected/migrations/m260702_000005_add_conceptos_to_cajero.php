<?php

class m260702_000005_add_conceptos_to_cajero extends CDbMigration
{
	private $operations = array(
		'action_conceptos_admin' => 'Administrar conceptos de cobro',
		'action_conceptos_create' => 'Crear conceptos de cobro',
		'action_conceptos_update' => 'Actualizar conceptos de cobro',
		'action_conceptos_view' => 'Ver conceptos de cobro',
		'action_conceptos_index' => 'Listar conceptos de cobro',
	);

	public function safeUp()
	{
		foreach($this->operations as $name=>$description) {
			$this->insertAuthItemIfMissing($name, 0, $description);
		}

		foreach(array_keys($this->operations) as $operation) {
			$this->insertAuthChildIfMissing('cajero', $operation);
		}

		foreach(array('admin', 'administrador') as $role) {
			if($this->authItemExists($role)) {
				foreach(array_keys($this->operations) as $operation) {
					$this->insertAuthChildIfMissing($role, $operation);
				}
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

		foreach(array_keys($this->operations) as $name) {
			$this->delete('cruge_authitem', 'name = :name', array(':name'=>$name));
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
		if($count === 0) {
			$this->insert('cruge_authitemchild', array('parent'=>$parent, 'child'=>$child));
		}
	}

	private function authItemExists($name)
	{
		$count = (int)$this->dbConnection->createCommand('SELECT COUNT(*) FROM cruge_authitem WHERE name = :name')
			->queryScalar(array(':name'=>$name));
		return $count > 0;
	}
}
