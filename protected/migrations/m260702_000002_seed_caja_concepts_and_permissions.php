<?php

class m260702_000002_seed_caja_concepts_and_permissions extends CDbMigration
{
	private $operations = array(
		'action_ingresos_create' => 'Registrar pagos de equipos',
		'action_ingresos_admin' => 'Administrar recibos',
		'action_ingresos_view' => 'Ver recibos',
		'action_ingresos_update' => 'Actualizar recibos',
		'action_ingresos_reciboPdf' => 'Imprimir recibos PDF',
		'action_ingresos_arqueoCaja' => 'Consultar arqueo de caja',
		'action_ingresos_anular' => 'Anular recibos',
		'action_ingresos_ingresosequipo' => 'Consultar ingresos por equipo',
		'action_ingresos_ArancelFecha' => 'Consultar aranceles por fecha',
		'action_ingresos_IngresoPortipo' => 'Consultar ingresos por tipo',
	);

	public function safeUp()
	{
		$this->execute("UPDATE conceptos SET Nombre = 'Arancel semanal' WHERE Nombre = 'Arancel'");
		$this->insertConceptIfMissing('Cuota societaria', 0);
		$this->insertConceptIfMissing('Multas', 0);

		$this->insertAuthItemIfMissing('cajero', 2, 'Rol para registrar pagos, emitir recibos y consultar caja');
		foreach($this->operations as $name=>$description) {
			$this->insertAuthItemIfMissing($name, 0, $description);
		}

		foreach(array(
			'action_ingresos_create',
			'action_ingresos_admin',
			'action_ingresos_view',
			'action_ingresos_reciboPdf',
			'action_ingresos_arqueoCaja',
			'action_ingresos_ingresosequipo',
			'action_ingresos_ArancelFecha',
			'action_ingresos_IngresoPortipo',
		) as $operation) {
			$this->insertAuthChildIfMissing('cajero', $operation);
		}

		foreach(array('admin', 'administrador') as $role) {
			if($this->authItemExists($role)) {
				$this->insertAuthChildIfMissing($role, 'cajero');
				$this->insertAuthChildIfMissing($role, 'action_ingresos_update');
				$this->insertAuthChildIfMissing($role, 'action_ingresos_anular');
			}
		}
	}

	public function safeDown()
	{
		foreach(array('admin', 'administrador', 'cajero') as $parent) {
			if($this->authItemExists($parent)) {
				foreach(array_merge(array('cajero'), array_keys($this->operations)) as $child) {
					$this->delete('cruge_authitemchild', 'parent = :parent AND child = :child', array(':parent'=>$parent, ':child'=>$child));
				}
			}
		}
		foreach(array_keys($this->operations) as $name) {
			$this->delete('cruge_authitem', 'name = :name', array(':name'=>$name));
		}
		$this->delete('cruge_authitem', 'name = :name', array(':name'=>'cajero'));
		$this->delete('conceptos', 'Nombre = :nombre', array(':nombre'=>'Cuota societaria'));
		$this->execute("UPDATE conceptos SET Nombre = 'Arancel' WHERE Nombre = 'Arancel semanal'");
	}

	private function insertConceptIfMissing($nombre, $monto)
	{
		$count = (int)$this->dbConnection->createCommand('SELECT COUNT(*) FROM conceptos WHERE Nombre = :nombre')
			->queryScalar(array(':nombre'=>$nombre));
		if($count === 0) {
			$this->insert('conceptos', array('Nombre'=>$nombre, 'Monto'=>$monto));
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
