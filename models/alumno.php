<?php

class Alumno {
	/**
	 * Obtiene: titulaciones, cursos, asignaturas y profesores asociados a un alumno
	 * @param int $userid el id del usuario
	 * @return stdclass[] devuelve la información asociada al alumno
	 */
	static function &getUniData($userid) {
		$data = array();
		$db = Database::getInstance();
		
		//obtener los asignaturas (y el id de profesor) de las que el alumno está matriculado
		$data['profesores_asignaturas'] = $db->loadObjectList('SELECT ua.usuario AS profesor,ua.asignatura FROM #__usuarios_asignaturas AS ua LEFT JOIN #__usuarios AS u ON u.id=ua.usuario WHERE ua.asignatura IN (SELECT asignatura FROM #__usuarios_asignaturas WHERE usuario='.$db->scape($userid).') AND u.type='.$db->scape(User::TYPE_PROFESOR));
		
		//obtener la información de los profores
		$ids = array();
		foreach ($data['profesores_asignaturas'] as $d) {
			$ids[] = $d->profesor;
		}
		$data['profesores'] = $db->loadObjectList('SELECT id,nombre FROM #__usuarios WHERE id IN ('.implode(',', $ids).')');
		
		//obtener la infirmación de las asignaturas
		$ids = array();
		foreach ($data['profesores_asignaturas'] as $d) {
			$ids[] = $d->asignatura;
		}
		$data['asignaturas'] = $db->loadObjectList('SELECT * FROM #__asignaturas WHERE id IN ('.implode(',', $ids).')');
		
		//obtener la infirmación de los cursos
		$ids = array();
		foreach ($data['asignaturas'] as $d) {
			$ids[] = $d->curso;
		}
		$data['cursos'] = $db->loadObjectList('SELECT * FROM #__cursos WHERE id IN ('.implode(',', $ids).')');
		
		//obtener la información de las titulaciones
		$ids = array();
		foreach ($data['cursos'] as $d) {
			$ids[] = $d->titulacion;
		}
		$data['titulaciones'] = $db->loadObjectList('SELECT * FROM #__titulaciones WHERE id IN ('.implode(',', $ids).')');
		
		return $data;
	}
}
