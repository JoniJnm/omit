<?php

session_start();

function get($k, $def='') {
	return isset($_GET[$k]) ? $_GET[$k] : $def;
}
function post($k, $def='') {
	return isset($_POST[$k]) ? $_POST[$k] : $def;
}
class Session {
	static function load($k, $def='') {
		if (isset($_SESSION[$k])) {
			return is_string($_SESSION[$k]) ? unserialize($_SESSION[$k]) : $_SESSION[$k];
		}
		else
			return $def;
	}
	static function save($k, $v) {
		$_SESSION[$k] = serialize($v);
	}
}
function &getData() {
	static $data = null;
	if (is_array($data)) return $data;
	$data = array();
	$db = Database::getInstance();
	$data['titulaciones'] = $db->loadObjectList('SELECT * FROM #__titulaciones');
	$data['cursos'] = $db->loadObjectList('SELECT * FROM #__cursos');
	$data['asignaturas'] = $db->loadObjectList('SELECT * FROM #__asignaturas');
	$data['profesores'] = $db->loadObjectList('SELECT * FROM #__profesores');
	$data['profesores_asignaturas'] = $db->loadObjectList('SELECT * FROM #__profesores_asignaturas');
	return $data;
}
function &getPreguntasDefault() {
	static $data = null;
	if (is_array($data)) return $data;
	$db = Database::getInstance();
	$data = $db->loadObjectList('SELECT * FROM #__preguntas_default');
	return $data;
}