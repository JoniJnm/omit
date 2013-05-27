<?php

/**
 * Controlador del profesor
 */

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance(User::TYPE_PROFESOR)->isLoged()) {
	$task = Request::both('task');

	if ($task == 'getPreguntas') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if (!$asignatura) exit;
		load('models.profesor');
		$data = Profesor::getPreguntas($profesor, $asignatura);
		echo json_encode($data);
	}
	elseif ($task == 'guardarPreguntas') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = intval(Request::post('asignatura'));
		if ($asignatura <= 0) exit;
		$db = Database::getInstance();
		$prefix = 'pregunta_';
		$prefix_len = strlen($prefix);
		
		$preg = 0;
		foreach ($_POST as $key=>$val) {
			if (substr($key, 0, $prefix_len) == $prefix) {
				if (Request::post('def')) {
					$db->query('INSERT INTO #__preguntas (pregunta, profesor, asignatura) 
					VALUES ('.$db->scape($val).', '.$db->scape($profesor).', '.$db->scape($asignatura).')');
				}
				else {
					$id = substr($key, $prefix_len);
					$db->query('UPDATE #__preguntas 
						set pregunta='.$db->scape($val).', profesor='.$db->scape($profesor).', asignatura='.$db->scape($asignatura).'
						WHERE id='.$db->scape($id));
				}
				$preg++;
				if ($preg >= 10) break;
			}
		}
		load('models.solr');
		Solr::delValoraciones($profesor, $asignatura);
		User::getInstance(User::TYPE_PROFESOR)->toHome();
	}
	elseif ($task == 'getComentarios' || $task == 'getClusters' || $task == 'getOpiniones') {
		load('models.uni');
		load('models.solr');
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if ($asignatura <= 0) exit;
		$desde = Request::post('desde', Uni::getDefaultDesde());
		$hasta = Request::post('hasta', Uni::getDefaultHasta());
		
		$buscar = trim(Request::post('buscar', ''));
		if ($buscar) $buscar = Solr::scapeQuery($buscar);
		else $buscar = "*:*";
		$buscar .= " AND fecha:[".Solr::convertDate($desde)." TO ".Solr::convertDate($hasta)."]";
		//$buscar .= " AND asignatura:".$asignatura; //TODO: Eliminar Descomentar línea
		
		$ids = trim(Request::post('ids', ''));
		if ($ids) $buscar .= " AND id:(".$ids.")";
		
		$opinion = intval(Request::post('opinion', -1));
		if ($opinion >= 0) $buscar .= " AND opinion:".$opinion;
		
		header('Content-type: application/json');
		
		if ($task == 'getComentarios') {
			$start = Request::post('start', 0);
			if ($start < 0) exit;
			$r = Solr::getComentarios($buscar, $start, 10);
			echo $r->getRawResponse();
		}
		elseif ($task == 'getClusters') {
			$clusters = Solr::getClusters($buscar);
			echo json_encode($clusters);
		}
		elseif ($task == 'getOpiniones') {
			$r = Solr::getComentarios($buscar, 0, 100000, array('fl' => 'opinion'));
			echo $r->getRawResponse();
		}
	}
	elseif ($task == 'getValoraciones') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if ($asignatura <= 0) exit;
		load('models.solr');
		$data = solr::getValoraciones($profesor, $asignatura);
		header('Content-type: application/json');
		echo json_encode($data);
	}
	elseif ($task == 'uploadDataCsv') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if ($asignatura <= 0) exit;
		if (substr($_FILES['csv_file']['name'], -4) != '.csv') {
			Mensajes::addAlerta("Sólo se admiten ficheros csv");
			User::getInstance(User::TYPE_PROFESOR)->toHome();
		}
		$data = @file_get_contents($_FILES['csv_file']['tmp_name']);
		$lines = explode("\n", $data);
		$len = count($lines);
		if ($len < 14) {
			Mensajes::addAlerta("Formato de archivo csv no soportado");
			User::getInstance(User::TYPE_PROFESOR)->toHome();
		}
		$users = array();
		for ($i=13; $i<$len; $i++) {
			$line = explode(';', $lines[$i]);
			$trim = " \t\n\r\0\x0B ";
			if (count($line) != 17 || !trim($line[7], $trim)) break;
			$users[] = (object)array(
				'apellido1' => utf8_encode(trim($line[1], $trim)),
				'apellido2' => utf8_encode(trim($line[2], $trim)),
				'nombre' => utf8_encode(trim($line[3], $trim)),
				'dni' => utf8_encode(trim($line[4], $trim)),
				'email' => utf8_encode(trim($line[7], $trim))
			);
		}
		if (!count($users)) {
			Mensajes::addAlerta("Formato de archivo csv no soportado");
			User::getInstance(User::TYPE_PROFESOR)->toHome();
		}
		$db = Database::getInstance();
		foreach ($users as $user) {
			$db->query('INSERT IGNORE INTO #__usuarios (email, apellido1, apellido2, nombre, pass, type) VALUES ('.$db->scape($user->email).', '.$db->scape($user->apellido1).', '.$db->scape($user->apellido2).', '.$db->scape($user->nombre).', '.$db->scape(md5($user->dni)).', '.User::TYPE_ALUMNO.') ON DUPLICATE KEY UPDATE apellido1=VALUES(apellido1), apellido2=VALUES(apellido2), nombre=VALUES(nombre)');
			$id = $db->loadResult('SELECT id FROM #__usuarios WHERE email='.$db->scape($user->email));
			$db->query('INSERT IGNORE INTO #__usuarios_asignaturas (usuario, asignatura) VALUES ('.$db->scape($id).', '.$db->scape($asignatura).')');
		}
		Mensajes::addInfo("Se han cargado ".count($users)." alumnos");
		User::getInstance(User::TYPE_PROFESOR)->toHome();
	}
}