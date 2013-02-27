<?php

require_once(dirname(__file__).'/config.php');

$types = User::getUserTypes();
foreach ($types as $type) {
	!Redirect::isLoged($type) or Redirect::toHome($type);
}