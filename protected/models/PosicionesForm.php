<?php


class PosicionesForm extends CFormModel
{
	public $idTorneo;
	
	
	public function rules()
	{
		return array(
		);
	}
	
	
	public function attributeLabels()
	{
		return array(
			'idTorneo'=>'Torneo',
		);
	}
	
}
	