<?php

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance(User::TYPE_ALUMNO)->isLoged()) {
	$task = Request::both('task');
	if ($task == 'insertarComentario') {
		load('models.preguntas');
		$asignatura = intval(Request::post('asignatura'));
		$profesor = intval(Request::post('profesor'));
		$comentario = trim(Request::post('comentario'));
		$usuario = User::getInstance('alumno');
		$respuestas = Preguntas::parsearRespuestas(Request::post('respuestas'));
		
		if ($asignatura > 0 && $profesor > 0 && $comentario && $usuario) {
			load('models.solr');
			Solr::addComentario($usuario->getId(), $profesor, $asignatura, $comentario, $respuestas);
			echo "OK";
		}
	}
	elseif ($task == 'getPreguntas') {
		$asignatura = intval(Request::post('asignatura'));
		$profesor = intval(Request::post('profesor'));
		if ($asignatura > 0 && $profesor > 0) {
			$data = new stdclass;
			$data->def = 0;
			$db = Database::getInstance();
			$data->preguntas = $db->loadObjectList('SELECT id,pregunta FROM #__preguntas WHERE asignatura='.$db->scape($asignatura).' AND profesor='.$db->scape($profesor));
			if (!$data->preguntas) {
				$data->def = 1;
				$data->preguntas = $db->loadObjectList('SELECT id,pregunta FROM #__preguntas_default');
			}
			header('Content-type: application/json');
			echo json_encode($data);
		}
	}
}