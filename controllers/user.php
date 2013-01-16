<?php

require_once(dirname(dirname(__file__)).'/config.php');

if (Session::load('isUser')) {
	$db = Database::getInstance();
	$task = get('task', post('task'));
	if ($task == 'insertarComentario') {
		include_once(PHP_LIBS."solr/Service.php");
		$solr = new Apache_Solr_Service(SOLR_SERVER, SOLR_PORT, SOLR_PATH);
		$r = $solr->search('*:*', 0, 1, array('sort' => 'id desc', 'fl' => 'id'));
		$id = $r->response->numFound > 0 ? $r->response->docs[0]->id+1 : 1;
		
		$d = new Apache_Solr_Document();
		$d->fecha = "NOW";
		$d->asignatura = intval(get('asignatura'));
		$d->profesor = intval(get('profesor'));
		$d->comentario = get('comentario');
		$d->id = $id;
		
		$solr->addDocument($d);
		$solr->commit();
		$solr->optimice();
	}
}