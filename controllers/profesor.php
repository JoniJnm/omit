<?php

require_once(dirname(dirname(__file__)).'/init/init.php');

if (User::getInstance(User::TYPE_PROFESOR)->isLoged()) {
	$task = Request::both('task');

	if ($task == 'getAsignaturas') {
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
	elseif ($task == 'getComentarios') {
		$profesor = User::getInstance(User::TYPE_PROFESOR)->getId();
		$asignatura = Request::post('asignatura');
		$start = Request::post('start', 0);
		$buscar = urlencode(htmlspecialchars(trim(Request::post('buscar'))));
		if (!$buscar) $buscar = '*:*';
		if ($asignatura <= 0 || $start < 0) exit;
		load('models.solr');
		$r = Solr::getComentarios($buscar, $start, 10);
		header('Content-type: application/json');
		echo $r->getRawResponse();
	}
}