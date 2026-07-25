<?php

/**
 * Helpers de sesion para el prode. Usa la sesion estandar de Yii
 * pero con una key propia (prodeUserId) para no pisar Cruge / Yii::app()->user.
 *
 * Reglas:
 *  - El id de usuario del prode vive en Yii::app()->session['prodeUserId'].
 *  - ProdeSession::user() carga el ProdeUsuario o devuelve null.
 *  - ProdeSession::login() setea la sesion y actualiza lastLogin.
 *  - ProdeSession::logout() mata la sesion del prode (no la de Cruge).
 */
class ProdeSession
{
	const SESSION_KEY = 'prodeUserId';

	/**
	 * Devuelve el usuario logueado, o null si no hay sesion valida.
	 *
	 * @return ProdeUsuario|null
	 */
	public static function user()
	{
		$id = self::getId();
		if ($id === null) {
			return null;
		}
		$user = ProdeUsuario::model()->findByPk($id);
		if ($user === null || !$user->activo) {
			self::clear();
			return null;
		}
		return $user;
	}

	/**
	 * Devuelve el id del usuario logueado, o null.
	 */
	public static function getId()
	{
		if (!Yii::app()->hasComponent('session')) {
			return null;
		}
		$session = Yii::app()->session;
		if (!isset($session[self::SESSION_KEY])) {
			return null;
		}
		$id = (int)$session[self::SESSION_KEY];
		return $id > 0 ? $id : null;
	}

	public static function isGuest()
	{
		return self::getId() === null;
	}

	/**
	 * Inicia sesion para un usuario. No toca Cruge / Yii::app()->user.
	 */
	public static function login(ProdeUsuario $user)
	{
		$user->lastLogin = date('Y-m-d H:i:s');
		$user->save(false);
		Yii::app()->session[self::SESSION_KEY] = (int)$user->idProdeUsuario;
	}

	public static function clear()
	{
		if (Yii::app()->hasComponent('session')) {
			unset(Yii::app()->session[self::SESSION_KEY]);
		}
	}

	public static function logout()
	{
		self::clear();
	}

	/**
	 * Lanza 403 si no hay sesion del prode.
	 */
	public static function requireLogin()
	{
		if (self::isGuest()) {
			throw new CHttpException(403, 'Tenes que iniciar sesion para acceder al prode.');
		}
	}

	/**
	 * Lanza 403 si el usuario del prode no es admin.
	 */
	public static function requireAdmin()
	{
		$user = self::user();
		if ($user === null) {
			throw new CHttpException(403, 'Tenes que iniciar sesion para acceder al prode.');
		}
		if (!$user->esAdmin) {
			throw new CHttpException(403, 'No tenes permisos de admin para esta accion.');
		}
	}
}
