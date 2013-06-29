<?php

class Solr {
	/**
	 * Caracteres especiales de solr que se deben escapar
	 * @var string[]
	 */
	private static $SPECIAL_CHARS = array('+','-','!','(',')','{','}','[',']','^','"','~','*','?',':',"\\");
	
	/**
	 * Etiquetas inválidas en el listado para cuando se vaya a hacer clustering
	 * @var string[]
	 */
	private static $INVALID_LABELS = array('Other Topics');
	
	/**
	 * Iniciales de los meses del año
	 * @var string[]
	 */
	private static $MESES = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nom', 'Dic');
	
	/**
	 * Objeto que interaccionará con Solr a partir de sockets
	 * @var Apache_Solr_Service 
	 */
	static private $solr = null;
	
	/**
	 * Inicia el objeto $solr para poder hacer futuras conexiones
	 */
	public function initSolr() {
		if (self::$solr === null) {
			load('libs.solr.Service');
			self::$solr = new Apache_Solr_Service(SOLR_SERVER, SOLR_PORT, SOLR_PATH);
		}
	}
	
	/**
	 * Añade un comentario en solr
	 * @param int $usuario id del usuario que hizo el comentario
	 * @param int $profesor id del profesor del que opina
	 * @param int $asignatura id de la asignatura
	 * @param string $comentario el comentario
	 * @param string[] $valoraciones cada valor del array tiene id_pregunta:valor_respuesta (valor entre 1 y 5)
	 */
	static function addComentario($usuario, $profesor, $asignatura, $comentario, $valoraciones) {
		load('helpers.opinion');
		
		//buscar el último id de comentario en solr para aumentarlo en 1 y asignarselo al nuevo comentario
		$r = self::$solr->search('*:*', 0, 1, array('sort' => 'id desc', 'fl' => 'id'));
		$id = $r->response->numFound > 0 ? $r->response->docs[0]->id + 1 : 1;
		
		$d = new Apache_Solr_Document();
		$d->fecha = "NOW";
		$d->usuario = $usuario;
		$d->profesor = $profesor;
		$d->asignatura = $asignatura;
		$d->comentario = str_replace("\n", "<br />", htmlspecialchars($comentario));
		$d->opinion = Opinion::clasificar($d->comentario); //clasificar en negativa, neutral o positiva
		$d->respuesta = $valoraciones;
		$d->id = $id;
		
		self::$solr->addDocument($d); //preparar comentario en la db de solr
		self::$solr->commit(); //insertar comentario
		self::$solr->optimize(); //actualizar índices de columnas
	}
	
	/**
	 * Borras las valoraciones de un profesor en una determinada asignatura
	 * Es usado cuando el profesor modifica las preguntas de una de sus asignaturas
	 * @param int $profesor id del profesor
	 * @param int $asignatura id de la asignatura
	 */
	static function delValoraciones($profesor, $asignatura) {
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
	 * Borra todos los comentarios y valoraciones de la base de datos Lucene
	 */
	static function deleteAll() {
		self::$solr->deleteByQuery('*:*');
		self::$solr->commit();
		self::$solr->optimize();
	}
	
	/**
	 * Obtiene los comentarios de solr dados unos parámetros
	 * @param string $query consulta sobre la db
	 * @param int $offset 
	 * @param int $limit
	 * @param string[] $params
	 * @return Apache_Solr_Response
	 */
	static function getComentarios($query, $offset = 0, $limit = 10, $params = array()) {
		$r = self::$solr->search($query, $offset, $limit, $params);
		return $r;
	}
	
	/**
	 * Sobre una consulta db se aplica clustering y se devuelven las etiquetas
	 * @param string $query
	 * @return string[] array con las etiquetas
	 */
	static function getClusters($query) {
		//aplica clustering sobre los 1000 primeros comentarios 
		//además sólo devuelve los id de los comentarios
		//para no tener que enviar el texto de cada comentario y demás información
		//no relevante en esta parte
		$r = self::$solr->clustering($query, 1000, array('fl' => 'id')); 
		$out = array();
		foreach ($r->clusters as $cluster) {
			foreach ($cluster->labels as $label) {
				if (in_array($label, self::$INVALID_LABELS)) continue;
				$out[] = $label;
			}
		}
		return $out;
	}
	
	/**
	 * Obtiene las valoraciones de un profesor sobre una asignatura
	 * 
	 * La salida es un array de objetos con la siguiente información:
	 * · mes: nombre del mes y la cantidad de comentarios en ese mes
	 * · series: array de objetos con:
	 *		· name: será "Preg i" donde i es el número de pregunta
	 *		· series: array con un único valor: la media de valoraciones de esa pregunta en el mes
	 * 
	 * @param int $profesor el id del profesor
	 * @param int $asignatura el id de la asignatura
	 * @return stdclass[]
	 */
	static function &getValoraciones($profesor, $asignatura) {
		$query = "profesor:$profesor AND asignatura:$asignatura AND respuesta:*";
		$r = self::getComentarios($query, 0, 1000, array('fl' => 'fecha respuesta'));
		$data = array();
		foreach ($r->response->docs as $doc) {
			$fecha = $doc->fecha;
			$valoraciones = $doc->respuesta;
			$mes = self::getMonthBYFecha($fecha);
			if (!isset($data[$mes])) {
				for ($i=0; $i<count($valoraciones); $i++) {
					$data[$mes][$i] = new stdclass;
					$data[$mes][$i]->valoraciones = array(); //array con las diferentes valoraciones (de 1 a 5)
					$data[$mes][$i]->count = 0; //cantidad de valoraciones para ese mes
					$data[$mes][$i]->suma = 0; //suma total de las valoraciones para ese mes
				}
			}
			foreach ($valoraciones as $i=>$res) {
				$res = explode(':', $res);
				$data[$mes][$i]->valoraciones[] = $res[1];
				$data[$mes][$i]->suma += $res[1];
				$data[$mes][$i]->count++;
			}
		}
		foreach ($data as $mes => $val) {
			foreach ($val as $i=>$d) {
				$data[$mes][$i]->media = $d->suma/$d->count; //calcular media para el mes
			}
		}
		//preparar objeto de salida
		$out = array();
		foreach ($data as $mes => $val) {
			$obj = new stdclass;
			$obj->mes = array($mes.' ('.$val[0]->count.')');
			foreach ($val as $i=>$d) {
				$obj->series[$i] = new stdclass;
				$obj->series[$i]->name = "Preg ".($i+1);
				$data[$mes][$i]->media = $d->suma/$d->count;
				$obj->series[$i]->data = array($data[$mes][$i]->media);
			}
			$out[] = $obj;
		}
		return $out;
	}
	
	/**
	 * Dada una fecha devuelve el nombre del mes
	 * @param string $fecha con formato dd-mm-[yy]yy o [yy]yy-mm-dd
	 * @return string el nombre del mes
	 */
	static private function getMonthBYFecha($fecha) {
		$fecha = substr($fecha, 0, 10);
		$fecha = explode('-', $fecha);
		$mes = intval($fecha[1]);
		return self::$MESES[$mes];
	}
	
	/**
	 * Convierte una fecha de tipo dd-mm-[yy]yy o [yy]yy-mm-dd a formato fecha de solr
	 * Las separaciones pueden ser guiones (-), espacios ( ) o barras (/)
	 * @param string $date la fecha a convertir
	 * @return string fecha convertida para ser insertada en solr
	 */
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
	
	/**
	 * Escapa los caracteres espaciales de solr sobre una consulta (en realidad los elimina)
	 * @param string $query consulta a escapar
	 * @return string consulta preparada para solr
	 */
	static function scapeQuery($query) {
		$frase = strpos($query, '"') !== false;
		$query = str_replace(self::$SPECIAL_CHARS, " ", $query);
		if ($frase) $query = '"'.$query.'"';
		return $query;
	}
}

Solr::initSolr();