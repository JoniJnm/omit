<?php

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance(User::TYPE_ADMIN)->isLoged()) {
	$db = Database::getInstance();
	$task = Request::both('task');

	if ($task == 'getXML') {
		load('models.uni');
		load('helpers.xml');
		$data = Uni::getData();
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=data.xml");
		header('Content-Type: application/xml; charset=UTF-8');
		XML::printUniDataObj($data);
		exit;
	}
	elseif ($task == 'uploadDataXml') {
		load('helpers.xml');
		$data = XML::getXMLFromFileUploaded('xml_data');
		if ($data) {
			$db->query('TRUNCATE `#__asignaturas`');
			$db->query('TRUNCATE `#__cursos`');
			$db->query('TRUNCATE `#__preguntas`');
			$db->query('TRUNCATE `#__usuarios`');
			$db->query('TRUNCATE `#__profesores_asignaturas`');
			$db->query('TRUNCATE `#__titulaciones`');
			foreach ($data['titulaciones']['titulacion'] as $d) {
				$db->query('INSERT INTO #__titulaciones (id,nombre) VALUES ('.$db->scape($d['id']).', '.$db->scape($d['nombre']).')');
			}
			foreach ($data['cursos']['curso'] as $d) {
				$db->query('INSERT INTO #__cursos (id, nombre, titulacion) VALUES ('.$db->scape($d['id']).', '.$db->scape($d['nombre']).', '.$db->scape($d['titulacion']).')');
			}
			foreach ($data['asignaturas']['asignatura'] as $d) {
				$db->query('INSERT INTO #__asignaturas (id, nombre, curso) VALUES ('.$db->scape($d['id']).', '.$db->scape($d['nombre']).', '.$db->scape($d['curso']).')');
			}
			foreach ($data['usuarios']['usuario'] as $d) {
				$db->query('INSERT INTO #__usuarios (id, email, apellido1, apellido2, nombre, pass, type) VALUES ('.$db->scape($d['id']).', '.$db->scape($d['email']).', '.$db->scape($d['apellido1']).', '.$db->scape($d['apellido2']).', '.$db->scape($d['nombre']).', '.$db->scape($d['pass']).', '.$db->scape($d['type']).')');
			}
			foreach ($data['usuarios_asignaturas']['usuario_asignatura'] as $d) {
				$db->query('INSERT INTO #__usuarios_asignaturas (usuario, asignatura) VALUES ('.$db->scape($d['usuario']).', '.$db->scape($d['asignatura']).')');
			}
		}
		Mensajes::addInfo('¡Datos cargados!');
		User::getInstance(User::TYPE_ADMIN)->toHome();
	}
}