<?php

class Uni {
	static function &getData() {
		static $data = null;
		if (is_array($data)) return $data;
		$data = array();
		$db = Database::getInstance();
		
		$data['titulaciones'] = $db->loadObjectList('SELECT * FROM #__titulaciones');
		$data['cursos'] = $db->loadObjectList('SELECT * FROM #__cursos ');
		$data['asignaturas'] = $db->loadObjectList('SELECT * FROM #__asignaturas');
		$data['usuarios'] = $db->loadObjectList('SELECT * FROM #__usuarios');
		$data['usuarios_asignaturas'] = $db->loadObjectList('SELECT * FROM #__usuarios_asignaturas');
		
		return $data;
	}
}
