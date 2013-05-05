<?php
class Opinion {
	const NEUTRAL = 0;
	const POSITIVO = 1;
	const NEGATIVO = 2;
	
	private static $MIN_DISTANCIA = 1000;
	private static $DATA_FILE = 'opinion_data.txt';
	private static $TOKENIZE_FROM = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	private static $TOKENIZE_TO = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	
	private $data = array();
	static private $instance = null;
	
	private function __construct() {
		$file = dirname(__FILE__).'/'.self::$DATA_FILE;
		$this->cargarDiccionario($file);
	}
	
	public static function clasificar($str) {
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance->_clasificar($str);
	}
	
	private function _clasificar($str) {
		preg_match_all('/[\w]+/', $str, $words);
		$pos = 0;
		$neg = 0;
		foreach ($words[0] as $word) {
			$word = $this->tokenize($word);
			if (isset($this->data[$word])) {
				$obj = $this->data[$word];
				if ($obj->tipo == 'pos') $pos += $obj->peso;
				elseif ($obj->tipo == 'neg') $neg += $obj->peso;
				else throw new Exception('Valor incorrecto para opinion ',print_r($obj, true));
			}
		}
		$diff = abs($pos-$neg);
		if ($diff < self::$MIN_DISTANCIA) return self::NEUTRAL;
		return $pos > $neg ? self::POSITIVO : self::NEGATIVO;
	}
	
	private function cargarDiccionario($file) {
		$data = file_get_contents($file);
		$data = explode("\n", $data);
		foreach ($data as $line) {
			if (!$line) continue;
			$line = explode("\t", $line);
			$word = $this->tokenize($line[0]);
			$this->data[$word] = (object)array('tipo' => $line[2], 'peso' => round($line[1]/1000));
		}
	}
	
	private function tokenize($word) {
		return strtolower(strtr($word, self::$TOKENIZE_FROM, self::$TOKENIZE_TO));
	}
}