<?php

require_once(dirname(__file__).'/config.php');

$types = User::getUserTypes();
foreach ($types as $type) {
	!User::isLoged($type) or User::toHome($type);
}