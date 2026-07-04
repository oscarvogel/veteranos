<?php

class m260702_000006_add_resumen_mensual_to_cajero extends CDbMigration
{
	private $operation = 'action_ingresos_resumenMensual';

	public function safeUp()
	{
		$this->insertAuthItemIfMissing($this->operation, 0, 'Consultar resumen mensual de caja');
		$this->insertAuthChildIfMissing('cajero', $this->operation);

		foreach(array('admin', 'administrador') as $role) {
			if($this->authItemExists($role)) {
				$this->insertAuthChildIfMissing($role, $this->operation);
			}
		}
	}

	public function safeDown()
	{
		foreach(array('admin', 'administrador', 'cajero') as $parent) {
			$this->delete('cruge_authitemchild', 'parent = :parent AND child = :child', array(
				':parent'=>$parent,
				':child'=>$this->operation,
			));
		}
		$this->delete('cruge_authitem', 'name = :name', array(':name'=>$this->operation));
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
