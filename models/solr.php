<?php

class Solr {
	static function addComentario($usuario, $profesor, $asignatura, $comentario, $respuestas) {
		load('libs.solr.Service');
		$solr = new Apache_Solr_Service(SOLR_SERVER, SOLR_PORT, SOLR_PATH);
		$r = $solr->search('*:*', 0, 1, array('sort' => 'id desc', 'fl' => 'id'));
		$id = $r->response->numFound > 0 ? $r->response->docs[0]->id + 1 : 1;
		
		$d = new Apache_Solr_Document();
		$d->fecha = "NOW";
		$d->usuario = $usuario; //username
		$d->profesor = $profesor;
		$d->asignatura = $asignatura;
		$d->comentario = $comentario;
		$d->respuesta = $respuestas;
		$d->id = $id;
		
		$solr->addDocument($d);
		$solr->commit();
		$solr->optimize();
	}
}