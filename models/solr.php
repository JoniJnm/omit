<?php

class Solr {
	const CLUSTER_METHOD = "solr"; //solr o java
	private static $SPECIAL_CHARS = array('+','-','!','(',')','{','}','[',']','^','"','~','*','?',':',"\\");
	private static $INVALID_LABELS = array('Other Topics');
	/**
	 * @var Apache_Solr_Service 
	 */
	static private $solr = null;
	
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
	
	static function getClusters($query) {
		if (self::CLUSTER_METHOD == 'java')
			return self::getClusters_JAVA($query);
		else
			return self::getClusters_SOLR($query);
	}
	
	static private function getClusters_SOLR($query) {
		self::initSolr();
		$r = self::$solr->clustering($query);
		if ($r->getHttpStatus() != 200) return array();
		$out = array();
		foreach ($r->clusters as $cluster) {
			foreach ($cluster->labels as $label) {
				$o = new stdclass;
				$o->label = $label;
				if (in_array($o->label, self::$INVALID_LABELS)) continue;
				$o->ids = $cluster->docs;
				$out[] = $o;
			}
		}
		return $out;
	}
	
	static private function getClusters_JAVA($query) {
		exec('java -jar '.PHP_JARS.'cluster.jar '.utf8_decode($query), $salida);
		if (!$salida || !is_array($salida)) return array();
		$out = array();
		foreach ($salida as $line) {
			if ($line == '__error') return array();
			if (!$line) continue;
			if (strpos($line, '|') === false) return array();
			
			$data = explode("|", $line);
			$o = new stdclass;
			$o->label = $data[0];
			if (in_array($o->label, self::$INVALID_LABELS)) continue;
			$o->ids = array();
			$ids = explode(",", $data[1]);
			foreach ($ids as $id) {
				if ($id) $o->ids[] = $id;
			}
			$out[] = $o;
		}
		return $out;
	}
	
	static function convertDate($date) {
		if (strpos($date, "/") !== false)
			$date = explode("/", $date);
		elseif (strpos($date, "-") !== false)
			$date = explode("-", $date);
		elseif (strpos($date, " ") !== false)
			$date = explode(" ", $date);
		
		if (count($date) != 3) return ""; //fecha no válida
		if (strlen($date[0]) == 4) { //formato yyyy-mm-dd a dd-mm-yyyy
			$tmp = $date[0];
			$date[0] = $date[2];
			$date[2] = $tmp;
		}
		
		return $date[2]."-".$date[1]."-".$date[0]."T00:00:00Z";
	}
	
	static function scapeQuery($query) {
		$frase = strpos($query, '"') !== false;
		$query = str_replace(self::$SPECIAL_CHARS, " ", $query);
		if ($frase) $query = '"'.$query.'"';
		return $query;
	}
}