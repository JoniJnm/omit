<?php

require_once(dirname(dirname(__file__)) . '/config.php');

if (Session::get('isAlumno')) {
	$task = Request::both('task');
	if ($task == 'insertarComentario') {
		load('models.preguntas');
		$asignatura = intval(Request::post('asignatura'));
		$profesor = intval(Request::post('profesor'));
		$comentario = trim(Request::post('comentario'));
		$usuario = Session::get('isAlumno');
		$respuestas = Preguntas::parsearRespuestas(Request::post('respuestas'));
		
		if ($asignatura > 0 && $profesor > 0 && $comentario && $usuario && $respuestas) {
			load('models.solr');
			Solr::addComentario($usuario, $profesor, $asignatura, $comentario, $respuestas);
			echo "OK";
		}
	}
}