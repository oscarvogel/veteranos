<?php

/**
 * Identity class para el prode. Usa la tabla prode_usuario y verifica
 * con password_verify. Es independiente de Cruge / UserIdentity / Yii::app()->user.
 *
 * Uso:
 *   $identity = new ProdeUserIdentity($email, $password);
 *   if ($identity->authenticate()) {
 *       ProdeSession::login($identity->getUser());
 *   }
 */
class ProdeUserIdentity extends CUserIdentity
{
	/** @var ProdeUsuario|null */
	private $_user;

	public function __construct($email, $password)
	{
		$this->username = trim((string)$email);
		$this->password = (string)$password;
	}

	public function authenticate()
	{
		$user = ProdeUsuario::findByEmail($this->username);
		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		}
		if (!$user->activo) {
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
			return false;
		}
		if (!$user->validatePassword($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return false;
		}
		$this->_user = $user;
		$this->errorCode = self::ERROR_NONE;
		return true;
	}

	/**
	 * Devuelve el usuario autenticado (despues de authenticate()).
	 */
	public function getUser()
	{
		return $this->_user;
	}

	public function getId()
	{
		return $this->_user ? (int)$this->_user->idProdeUsuario : 0;
	}
}
