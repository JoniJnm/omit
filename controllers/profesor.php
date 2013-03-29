<?php

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance(User::TYPE_PROFESOR)->isLoged()) {
	$task = Request::both('task');

	if ($task == 'getPreguntas') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if (!$asignatura) exit;
		$db = Database::getInstance();
		$data = $db->loadObjectList('
			SELECT id, pregunta FROM #__preguntas 
			WHERE profesor='.$db->scape($profesor).' AND asignatura='.$db->scape($asignatura).'
		');
		if (!$data)
			$data = $db->loadObjectList('SELECT pregunta FROM #__preguntas_default');
		header('Content-type: application/json');
		echo json_encode($data);
	}
	elseif ($task == 'guardarPreguntas') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = intval(Request::post('asignatura'));
		if ($asignatura <= 0) exit;
		$db = Database::getInstance();
		$prefix = 'pregunta_';
		$prefix_len = strlen($prefix);
		
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
			}
		}
		User::getInstance(User::TYPE_PROFESOR)->toHome();
	}
	elseif ($task == 'getComentarios' || $task == 'getClusters') {
		load('models.uni');
		load('models.solr');
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		if ($asignatura <= 0) exit;
		$desde = Request::post('desde', Uni::getDefaultDesde());
		$hasta = Request::post('hasta', Uni::getDefaultHasta());
		
		$buscar = trim(Request::post('buscar', ''));
		if ($buscar) $buscar = '"'.$buscar.'"';
		else $buscar = "*:*";
		$buscar .= " AND fecha:[".Solr::convertDate($desde)." TO ".Solr::convertDate($hasta)."]";
		//$buscar .= " AND asignatura:".$asignatura;
		
		$ids = trim(Request::post('ids', ''));
		if ($ids) {
			$buscar .= " AND id:(".$ids.")";
		}
		
		header('Content-type: application/json');
		
		if ($task == 'getComentarios') {
			$start = Request::post('start', 0);
			if ($start < 0) exit;
			try {
				$r = Solr::getComentarios($buscar, $start, 10);
				echo $r->getRawResponse();
			}
			catch(Exception $e) {
				echo json_encode(array());
			}
		}
		else {
			$clusters = Solr::getClusters($buscar);
			echo json_encode($clusters);
		}
	}
}