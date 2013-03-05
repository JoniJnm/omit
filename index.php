<?php

require_once(dirname(__file__).'/init/init.php');

$types = User::getUserTypes();
foreach ($types as $type) {
	User::getInstance($type)->toHomeIfLoged();
}
User::getInstance('alumno')->toLogin();