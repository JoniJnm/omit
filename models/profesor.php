<?php

class Profesor {
	/**
	 * Devuelve las asignaturas asociadas a un profesor
	 * @param int $userid ID del profesor
	 * @return stdclass[] la información de las asignaturas
	 */
	static function getAsignaturas($userid) {
		$db = Database::getInstance();
		$data = $db->loadObjectList('
			SELECT a.id, a.nombre, c.nombre AS curso 
			FROM #__asignaturas AS a
			LEFT JOIN #__cursos AS c ON c.id = a.curso
			WHERE a.id IN (
				SELECT asignatura FROM #__usuarios_asignaturas
				WHERE usuario='.$db->scape($userid).'
			)');
		return $data;
	}
	
	/**
	 * Devuelve las preguntas asociadas a un profesor y asignatura
	 * @param int $profesor id del profesor
	 * @param int $asignatura id de la asignatura
	 * @return stdclass[] Las preguntas
	 */
	static function getPreguntas($profesor, $asignatura) {
		$db = Database::getInstance();
		$data = $db->loadObjectList('
			SELECT id, pregunta FROM #__preguntas 
			WHERE profesor='.$db->scape($profesor).' AND asignatura='.$db->scape($asignatura).'
		');
		if (!$data)
			$data = $db->loadObjectList('SELECT pregunta FROM #__preguntas_default');
		return $data;
	}
	
	/**
	 * Importa los alumnos encontrados en formato csv en la asignatura deseada
	 * @param string $data Los datos de los alumnos en formato csv
	 * @param int $asignatura Id de los alumnos donde asociarlos
	 * @return int número de alumnos importados
	 */
	static function importAlumnosFromCSV(&$data, $asignatura) {
		load('helpers.csv');
		$users = CSV::getAlumnos($data);
		if (!count($users)) return -1;
		$db = Database::getInstance();
		foreach ($users as $user) {
			$db->query('INSERT IGNORE INTO #__usuarios (email, apellido1, apellido2, nombre, pass, type) VALUES ('.$db->scape($user->email).', '.$db->scape($user->apellido1).', '.$db->scape($user->apellido2).', '.$db->scape($user->nombre).', '.$db->scape(md5($user->dni)).', '.User::TYPE_ALUMNO.') ON DUPLICATE KEY UPDATE apellido1=VALUES(apellido1), apellido2=VALUES(apellido2), nombre=VALUES(nombre)');
			$id = $db->loadResult('SELECT id FROM #__usuarios WHERE email='.$db->scape($user->email));
			$db->query('INSERT IGNORE INTO #__usuarios_asignaturas (usuario, asignatura) VALUES ('.$db->scape($id).', '.$db->scape($asignatura).')');
		}
		return count($users);
	}
}