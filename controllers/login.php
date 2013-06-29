<?php

/**
 * Controlador para el acceso
 */

require_once(dirname(dirname(__file__)).'/init/init.php');

$task = Request::both('task');
if ($task == 'login') {
	$userType = Request::both('userType');
	$user = User::getInstance($userType);
	$email = Request::post('email');
	$pass = Request::post('pass');
	if ($user->login($email, $pass)) {
		$user->toHome();
	}
	Mensajes::addAlerta('Usuario o contraseña incorrectos.');
	$user->toLogin();
}
elseif ($task == 'salir') {
	$userType = Request::both('userType');
	User::logout($userType);
	Mensajes::addInfo("Te has desconectado correctamente");
	User::getInstance($userType)->toLogin();
}