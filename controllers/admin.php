<?php

/**
 * Controlador del administrador
 */
require_once(dirname(dirname(__file__)) . '/init/init.php');

if (User::getInstance(User::TYPE_ADMIN)->isLoged()) {
	$db = Database::getInstance();
	$task = Request::both('task');

	if ($task == 'getCSV') {
		load('models.uni');
		load('helpers.csv');
		$data = Uni::getData();
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=data.csv");
		header('Content-Type: text/csv; charset=ISO-8859-15');
		CSV::printUniDataObj($data);
		exit;
	} elseif ($task == 'uploadDataCsv') {
		load('helpers.csv');
		$data = CSV::getUniObjFromCSVUploaded('csv_data');
		$needs = array('titulaciones', 'cursos', 'asignaturas', 'usuarios', 'usuarios_asignaturas');
		$error = false;
		foreach ($needs as $n) {
			$error = !isset($data[$n]);
			if ($error) break;
		}
		if ($error) {
			Mensajes::addAlerta("Formato csv inválido");
		}
		else {
			$db->query('TRUNCATE `#__asignaturas`');
			$db->query('TRUNCATE `#__cursos`');
			$db->query('TRUNCATE `#__preguntas`');
			$db->query('TRUNCATE `#__usuarios`');
			$db->query('TRUNCATE `#__usuarios_asignaturas`');
			$db->query('TRUNCATE `#__titulaciones`');
			foreach ($data['titulaciones'] as $d) {
				$db->query('INSERT INTO #__titulaciones (id,nombre) VALUES (' . $db->scape($d->id) . ', ' . $db->scape($d->nombre) . ')');
			}
			foreach ($data['cursos'] as $d) {
				$db->query('INSERT INTO #__cursos (id, nombre, titulacion) VALUES (' . $db->scape($d->id) . ', ' . $db->scape($d->nombre) . ', ' . $db->scape($d->titulacion) . ')');
			}
			foreach ($data['asignaturas'] as $d) {
				$db->query('INSERT INTO #__asignaturas (id, nombre, curso) VALUES (' . $db->scape($d->id) . ', ' . $db->scape($d->nombre) . ', ' . $db->scape($d->curso) . ')');
			}
			foreach ($data['usuarios'] as $d) {
				$db->query('INSERT INTO #__usuarios (id, email, apellido1, apellido2, nombre, pass, type) VALUES (' . $db->scape($d->id) . ', ' . $db->scape($d->email) . ', ' . $db->scape($d->apellido1) . ', ' . $db->scape($d->apellido2) . ', ' . $db->scape($d->nombre) . ', ' . $db->scape($d->pass) . ', ' . $db->scape($d->type) . ')');
			}
			foreach ($data['usuarios_asignaturas'] as $d) {
				$db->query('INSERT INTO #__usuarios_asignaturas (usuario, asignatura) VALUES (' . $db->scape($d->usuario) . ', ' . $db->scape($d->asignatura) . ')');
			}
			Mensajes::addInfo('¡Datos cargados!');
		}
		User::getInstance(User::TYPE_ADMIN)->toHome();
	}
}