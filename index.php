<?php

require_once(dirname(__file__).'/init/init.php');

//Llevar a la página de usuario si está logeado. Sino a la de login
$types = User::getUserTypes();
foreach ($types as $type) {
	User::getInstance($type)->toHomeIfLoged();
}
User::getInstance(user::TYPE_ALUMNO)->toLogin();