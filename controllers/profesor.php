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
		$buscar .= " AND asignatura:".$asignatura;
		
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
		$asignatura = Request::post('asignatura');
		if ($asignatura <= 0) exit;
		if (substr($_FILES['csv_file']['name'], -4) != '.csv') {
			Mensajes::addAlerta("Sólo se admiten ficheros csv");
			User::getInstance(User::TYPE_PROFESOR)->toHome();
		}
		$data = @file_get_contents($_FILES['csv_file']['tmp_name']);
		load('modes.profesor');
		$imported = Profesor::importAlumnosFromCSV($data, $asignatura);
		if ($imported > 0)
			Mensajes::addInfo("Se han cargado ".$imported." alumnos");
		else
			Mensajes::addAlerta("Formato de archivo csv no soportado");
		User::getInstance(User::TYPE_PROFESOR)->toHome();
	}
}