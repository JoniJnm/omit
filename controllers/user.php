<?php

require_once(dirname(dirname(__file__)) . '/config.php');

if (Session::load('isUser')) {
	$task = get('task', post('task'));
	if ($task == 'insertarComentario') {
		include_once(PHP_LIBS . "solr/Service.php");
		$solr = new Apache_Solr_Service(SOLR_SERVER, SOLR_PORT, SOLR_PATH);
		$r = $solr->search('*:*', 0, 1, array('sort' => 'id desc', 'fl' => 'id'));
		$id = $r->response->numFound > 0 ? $r->response->docs[0]->id + 1 : 1;
		
		$res = array();
		$respuestas = post('respuestas');
		$respuestas = explode(';', $respuestas);
		foreach ($respuestas as $r) {
			$r = explode(':', $r);
			if (count($r) != 2) exit;
			if (!($r[0] > 0)) exit;
			if (!($r[1] >= 0 && $r[1] <= 5)) exit;
			$res[] = $r[0].':'.$r[1];
		}
		
		$d = new Apache_Solr_Document();
		$d->fecha = "NOW";
		$d->respuesta = $res;
		$d->asignatura = intval(post('asignatura'));
		$d->profesor = intval(post('profesor'));
		$d->comentario = trim(post('comentario'));
		$d->usuario = Session::load('isUser'); //username
		$d->id = $id;

		if ($d->asignatura > 0 && $d->profesor > 0 && $d->comentario) {
			$solr->addDocument($d);
			$solr->commit();
			$solr->optimize();
			echo "OK";
		}
	}
}