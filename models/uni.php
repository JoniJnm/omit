<?php

class Uni {
	static function &getData() {
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
}
