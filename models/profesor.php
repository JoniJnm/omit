<?php

class Profesor {
	static function getAsignaturas() {
		$db = Database::getInstance();
		$user = User::getInstance(User::TYPE_PROFESOR);
		$data = $db->loadObjectList('
			SELECT a.id, a.nombre, c.nombre AS curso 
			FROM #__asignaturas AS a
			LEFT JOIN #__cursos AS c ON c.id = a.curso
			WHERE a.id IN (
				SELECT asignatura FROM #__usuarios_asignaturas
				WHERE usuario='.$db->scape($user->getId()).'
			)');
		return $data;
	}
}