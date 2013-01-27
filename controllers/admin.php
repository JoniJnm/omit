<?php

require_once(dirname(dirname(__file__)).'/config.php');

if (Session::get('isAdmin')) {
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
			$db->query('TRUNCATE `#__profesores`');
			$db->query('TRUNCATE `#__profesores_asignaturas`');
			$db->query('TRUNCATE `#__titulaciones`');
			foreach ($data['titulaciones']['titulacion'] as $d) {
				$db->query('INSERT INTO #__titulaciones (id,nombre) VALUES ("'.addslashes($d['id']).'", "'.addslashes($d['nombre']).'")');
			}
			foreach ($data['cursos']['curso'] as $d) {
				$db->query('INSERT INTO #__cursos (id, nombre, titulacion) VALUES ("'.addslashes($d['id']).'", "'.addslashes($d['nombre']).'", "'.addslashes($d['titulacion']).'")');
			}
			foreach ($data['asignaturas']['asignatura'] as $d) {
				$db->query('INSERT INTO #__asignaturas (id, nombre, curso) VALUES ("'.addslashes($d['id']).'", "'.addslashes($d['nombre']).'", "'.addslashes($d['curso']).'")');
			}
			foreach ($data['profesores']['profesor'] as $d) {
				$db->query('INSERT INTO #__profesores (id, nombre) VALUES ("'.addslashes($d['id']).'", "'.addslashes($d['nombre']).'")');
			}
			foreach ($data['profesores_asignaturas']['profesor_asignatura'] as $d) {
				$db->query('INSERT INTO #__profesores_asignaturas (profesor, asignatura) VALUES ("'.addslashes($d['profesor']).'", "'.addslashes($d['asignatura']).'")');
			}
		}
		Mensajes::addMensaje('info', '¡Datos cargados!');
		header('location: '.HTML_URL.'admin.php', true, 301);
		exit;
	}
}