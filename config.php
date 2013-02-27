<?php

DEFINE('HTML_URL', 'http://localhost/uni/');

DEFINE("DB_SERVER", "localhost");
DEFINE("DB_USER", "root");
DEFINE("DB_PASS", "");
DEFINE("DB_NAME", "uni");
DEFINE("DB_PREFIX", "uni_");

DEFINE('SOLR_SERVER', 'localhost');
DEFINE('SOLR_PORT', 8983);
DEFINE('SOLR_PATH', '/solr/');

//config ends

DEFINE('PHP_PATH', dirname(__FILE__).'/');
DEFINE('PHP_LIBS', PHP_PATH.'/libs/');
DEFINE('PHP_HELPERS', PHP_PATH.'/helpers/');
DEFINE('PHP_MODELS', PHP_PATH.'/models/');
DEFINE('PHP_FILES', PHP_PATH.'/php/');
DEFINE('PHP_TPLS', PHP_PATH.'/tpls/');

DEFINE('ALUMNO_CONTROLLER', HTML_URL.'controllers/alumno.php');
DEFINE('PROFESOR_CONTROLLER', HTML_URL.'controllers/profesor.php');
DEFINE('ADMIN_CONTROLLER', HTML_URL.'controllers/admin.php');

require_once(PHP_FILES.'all.php');