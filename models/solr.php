<?php

class Solr {
	static $solr = null;
	
	static private function initSolr() {
		if (self::$solr === null) {
			load('libs.solr.Service');
			self::$solr = new Apache_Solr_Service(SOLR_SERVER, SOLR_PORT, SOLR_PATH);
		}
	}
	static function addComentario($usuario, $profesor, $asignatura, $comentario, $respuestas) {
		self::initSolr();
		$r = self::$solr->search('*:*', 0, 1, array('sort' => 'id desc', 'fl' => 'id'));
		$id = $r->response->numFound > 0 ? $r->response->docs[0]->id + 1 : 1;
		
		$d = new Apache_Solr_Document();
		$d->fecha = "NOW";
		$d->usuario = $usuario;
		$d->profesor = $profesor;
		$d->asignatura = $asignatura;
		$d->comentario = str_replace("\n", "<br />", htmlspecialchars($comentario));
		$d->respuesta = $respuestas;
		$d->id = $id;
		
		self::$solr->addDocument($d);
		self::$solr->commit();
		self::$solr->optimize();
	}
	/**
	 * 
	 * @return Apache_Solr_Response
	 */
	static function getComentarios($query, $offset = 0, $limit = 10, $params = array()) {
		self::initSolr();
		$r = self::$solr->search($query, $offset, $limit, $params);
		return $r;
	}
}