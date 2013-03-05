<?php

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance('alumno')->isLoged()) {
	$task = Request::both('task');
	if ($task == 'insertarComentario') {
		load('models.preguntas');
		$asignatura = intval(Request::post('asignatura'));
		$profesor = intval(Request::post('profesor'));
		$comentario = trim(Request::post('comentario'));
		$usuario = User::getInstance('alumno');
		$respuestas = Preguntas::parsearRespuestas(Request::post('respuestas'));
		
		if ($asignatura > 0 && $profesor > 0 && $comentario && $usuario && $respuestas) {
			load('models.solr');
			Solr::addComentario($usuario->getId(), $profesor, $asignatura, $comentario, $respuestas);
			echo "OK";
		}
	}
}