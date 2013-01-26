<?php

DEFINE('PHP_PATH', dirname(__FILE__).'/');
DEFINE('PHP_LIBS', PHP_PATH.'/libs/');
DEFINE('PHP_HELPERS', PHP_PATH.'/helpers/');
DEFINE('PHP_FILES', PHP_PATH.'/php/');
DEFINE('PHP_TPLS', PHP_PATH.'/tpls/');

DEFINE('HTML_URL', 'http://localhost/uni/');
DEFINE('ADMIN_CONTROLLER', HTML_URL.'controllers/admin.php');
DEFINE('USER_CONTROLLER', HTML_URL.'controllers/user.php');

DEFINE('SOLR_SERVER', 'localhost');
DEFINE('SOLR_PORT', 8983);
DEFINE('SOLR_PATH', '/solr/');

require_once(PHP_FILES.'all.php');
require_once(PHP_HELPERS.'database.php');
require_once(PHP_HELPERS.'mensajes.php');

class Config {
	var $server = 'localhost';
	var $user = 'root';
	var $pass = '';
	var $db = 'uni';
	var $pre = 'uni_';
	
	function &getInstance() {
		static $class = null;
		if (!is_object($class))
			$class = new Config;
		return $class;
	}
}