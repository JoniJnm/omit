<?php

class Profesor {
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
}