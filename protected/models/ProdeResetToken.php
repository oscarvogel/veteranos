<?php

/**
 * Token de recuperacion de password del prode.
 * Se crea cuando un usuario pide "olvide mi password", se valida al
 * canjearlo y queda con usadoEn != NULL.
 *
 * @property integer $idProdeReset
 * @property integer $idProdeUsuario
 * @property string $token
 * @property string $expiraEn
 * @property string $usadoEn
 * @property string $createdAt
 */
class ProdeResetToken extends CActiveRecord
{
	const TTL_HORAS = 2;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'prode_reset_token';
	}

	public function rules()
	{
		return array(
			array('idProdeUsuario, token, expiraEn, createdAt', 'required'),
			array('idProdeUsuario', 'numerical', 'integerOnly'=>true),
			array('token', 'length', 'is'=>64),
			array('expiraEn, usadoEn, createdAt', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'usuario' => array(self::BELONGS_TO, 'ProdeUsuario', 'idProdeUsuario'),
		);
	}

	/**
	 * Crea un token nuevo para el usuario. Invalida los anteriores.
	 */
	public static function crearPara($idProdeUsuario)
	{
		// Invalida los viejos
		self::model()->deleteAll('idProdeUsuario = :id AND usadoEn IS NULL', array(':id' => (int)$idProdeUsuario));

		$token = new self;
		$token->idProdeUsuario = (int)$idProdeUsuario;
		$token->token = bin2hex(openssl_random_pseudo_bytes(32));
		$token->expiraEn = date('Y-m-d H:i:s', time() + self::TTL_HORAS * 3600);
		$token->createdAt = date('Y-m-d H:i:s');
		$token->save();
		return $token;
	}

	/**
	 * Busca un token valido (no usado, no expirado).
	 */
	public static function buscarValido($token)
	{
		return self::model()->find('token = :t AND usadoEn IS NULL AND expiraEn > NOW()', array(
			':t' => $token,
		));
	}

	public function marcarUsado()
	{
		$this->usadoEn = date('Y-m-d H:i:s');
		$this->save();
	}
}
