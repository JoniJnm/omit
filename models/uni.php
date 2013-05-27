<?php

class Uni {
	/**
	 * Obtiene las titulaciones, cursos, asignaturas, alumnos y profesores de la universidad
	 * @return stdclass[] La información de la base de datos
	 */
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
	
	/**
	 * Devuelve la fecha "desde" por defecto, corresponde con el mes de septiembre del curso
	 * @return string
	 */
	static function getDefaultDesde() {
		$y = date("Y");
		$m = date("n");
		return "01/09/".($m < 9 ? $y-1 : $y);
	}
	
	/**
	 * Devuelve la fecha "hasta" por defecto, corresponde con el mes de septiembre del curso siguiente
	 * @return string
	 */
	static function getDefaultHasta() {
		$y = date("Y");
		$m = date("n");
		return "01/09/".($m < 9 ? $y : $y+1);
	}
}
