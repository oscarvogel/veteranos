<?php

class m260702_000003_assign_cajero_to_tesorero extends CDbMigration
{
	public function safeUp()
	{
		$userId = $this->getUserId('teo');
		if($userId === null)
			return;

		$this->insertAssignmentIfMissing($userId, 'cajero');
	}

	public function safeDown()
	{
		$userId = $this->getUserId('teo');
		if($userId !== null)
			$this->delete('cruge_authassignment', 'userid = :userid AND itemname = :itemname', array(
				':userid'=>$userId,
				':itemname'=>'cajero',
			));
	}

	private function getUserId($username)
	{
		$userId = $this->dbConnection->createCommand('SELECT iduser FROM cruge_user WHERE username = :username')
			->queryScalar(array(':username'=>$username));

		return $userId === false ? null : (int)$userId;
	}

	private function insertAssignmentIfMissing($userId, $itemName)
	{
		$count = (int)$this->dbConnection->createCommand(
			'SELECT COUNT(*) FROM cruge_authassignment WHERE userid = :userid AND itemname = :itemname'
		)->queryScalar(array(':userid'=>$userId, ':itemname'=>$itemName));

		if($count === 0) {
			$this->insert('cruge_authassignment', array(
				'userid'=>$userId,
				'itemname'=>$itemName,
				'bizrule'=>null,
				'data'=>null,
			));
		}
	}
}
