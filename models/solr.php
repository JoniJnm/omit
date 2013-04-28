<?php

class Solr {
	private static $SPECIAL_CHARS = array('+','-','!','(',')','{','}','[',']','^','"','~','*','?',':',"\\");
	private static $INVALID_LABELS = array('Other Topics');
	private static $MESES = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nom', 'Dic');
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
	static function delValoraciones($profesor, $asignatura) {
		self::initSolr();
		$query = "profesor:$profesor AND asignatura:$asignatura AND respuesta:*";
		$r = self::$solr->search($query, 0, 10000);
		foreach ($r->response->docs as $doc) {
			$d = new Apache_Solr_Document();
			$d->fecha = $doc->fecha;
			$d->usuario = $doc->usuario;
			$d->profesor = $doc->profesor;
			$d->asignatura = $doc->asignatura;
			$d->comentario = $doc->comentario;
			$d->respuesta = array();
			$d->id = $doc->id;
			self::$solr->addDocument($d);
		}
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
		self::initSolr();
		$r = self::$solr->clustering($query, 1000, array('fl' => 'id'));
		if ($r->getHttpStatus() != 200) return array();
		$out = array();
		foreach ($r->clusters as $cluster) {
			foreach ($cluster->labels as $label) {
				if (in_array($label, self::$INVALID_LABELS)) continue;
				$o = new stdclass;
				$o->label = $label;
				$out[] = $o;
			}
		}
		return $out;
	}
	
	static function getRespuestas($profesor, $asignatura) {
		self::initSolr();
		$query = "profesor:$profesor AND asignatura:$asignatura AND respuesta:*";
		$query = "profesor:1 AND asignatura:1 AND respuesta:*"; //TODO: Eliminar linea
		$r = self::$solr->search($query, 0, 1000, array('fl' => 'fecha respuesta'));
		$data = array();
		foreach ($r->response->docs as $doc) {
			$fecha = $doc->fecha;
			$respuestas = $doc->respuesta;
			$mes = self::getMonthBYFecha($fecha);
			if (!isset($data[$mes])) {
				for ($i=0; $i<count($respuestas); $i++) {
					$data[$mes][$i] = new stdclass;
					$data[$mes][$i]->respuestas = array();
					$data[$mes][$i]->count = 0;
					$data[$mes][$i]->suma = 0;
				}
			}
			foreach ($respuestas as $i=>$res) {
				$res = explode(':', $res);
				$data[$mes][$i]->respuestas[] = $res[1];
				$data[$mes][$i]->suma += $res[1];
				$data[$mes][$i]->count++;
			}
		}
		foreach ($data as $mes => $val) {
			foreach ($val as $i=>$d) {
				$data[$mes][$i]->media = $d->suma/$d->count;
			}
		}
		$out = new stdclass;
		$out->meses = array();
		$out->series = array();
		foreach ($data as $mes => $val) {
			$out->meses[] = $mes.' ('.$val[0]->count.')';
			foreach ($val as $i=>$d) {
				if (!isset($out->series[$i])) {
					$out->series[$i] = new stdclass;
					$out->series[$i]->name = "Preg ".($i+1);
					$out->series[$i]->data = array();
				}
				$data[$mes][$i]->media = $d->suma/$d->count;
				$out->series[$i]->data[] = $data[$mes][$i]->media;
			}
		}
		load('models.profesor');
		$preg = Profesor::getPreguntas($profesor, $asignatura);
		$out->preguntas = array();
		foreach ($preg as $p) {
			$out->preguntas[] = $p->pregunta;
		}
		return $out;
	}
	
	static private function getMonthBYFecha($fecha) {
		$fecha = substr($fecha, 0, 10);
		$fecha = explode('-', $fecha);
		$mes = intval($fecha[1]);
		return self::$MESES[$mes];
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