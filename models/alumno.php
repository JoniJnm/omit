<?php

class Alumno {
	static function &getUniData() {
		static $data = null;
		if (is_array($data)) return $data;
		$data = array();
		$db = Database::getInstance();
		
		$data['profesores_asignaturas'] = $db->loadObjectList('SELECT ua.usuario AS profesor,ua.asignatura FROM #__usuarios_asignaturas AS ua LEFT JOIN #__usuarios AS u ON u.id=ua.usuario WHERE ua.asignatura IN (SELECT asignatura FROM #__usuarios_asignaturas WHERE usuario='.$db->scape(User::getInstance('alumno')->getId()).') AND u.type='.$db->scape(User::TYPE_PROFESOR));
		
		$ids = array();
		foreach ($data['profesores_asignaturas'] as $d) {
			$ids[] = $d->profesor;
		}
		$data['profesores'] = $db->loadObjectList('SELECT id,name AS nombre FROM #__usuarios WHERE id IN ('.implode(',', $ids).')');
		
		$ids = array();
		foreach ($data['profesores_asignaturas'] as $d) {
			$ids[] = $d->asignatura;
		}
		$data['asignaturas'] = $db->loadObjectList('SELECT * FROM #__asignaturas WHERE id IN ('.implode(',', $ids).')');
		
		$ids = array();
		foreach ($data['asignaturas'] as $d) {
			$ids[] = $d->curso;
		}
		$data['cursos'] = $db->loadObjectList('SELECT * FROM #__cursos WHERE id IN ('.implode(',', $ids).')');
		
		$ids = array();
		foreach ($data['cursos'] as $d) {
			$ids[] = $d->titulacion;
		}
		$data['titulaciones'] = $db->loadObjectList('SELECT * FROM #__titulaciones WHERE id IN ('.implode(',', $ids).')');
		
		return $data;
	}
}
