<?php

/**
 * Clase para etiquetar un texto como neutral, negativo o positivo
 * 
 * El diccionario a cargar tendrá la siguiente estructura
 * 
 * palabra peso tipo contador
 * 
 * Palabra: el string, la palabra del diccionario
 * Peso: el peso asociado a la palabra
 * Tipo: tipo de palabra (neg, pos o mod)
 * Contador: en caso de que sea de tipo mod podrá tener un contador para saber a cuántas palabras 
 *			sucesivas se aplica el modificador
 */

class Opinion {
	const NEUTRAL = 0;
	const POSITIVO = 1;
	const NEGATIVO = 2;
	
	/**
	 * Mínima distancia entre puntuación negativa y positiva. Si no se cumple el string
	 * será neutral. Va en porcentaje
	 * @var float El porcentaje
	 */
	private static $MIN_DIFF = 0.2; //20%
	/**
	 * Nombre del fichero donde está el diccionario de palabras con su peso y tipo (neg, pos, mod)
	 * @var string nombre del fichero
	 */
	private static $DATA_FILE = 'opinion_data.txt';
	
	//Para quitar acentos de las letras
	private static $TOKENIZE_FROM = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	private static $TOKENIZE_TO = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	
	//El diccionario de palabras con el peso y el tipo de palabra
	private $data = array();
	
	/**
	 * Para hacer un singlenton
	 * @var Opinion 
	 */
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
		preg_match_all('/[\w]+/', $str, $words); //obtiene todas las palabras del string de entrada
		$pos = 0; //sumatorio del peso de las palabras positivas
		$neg = 0; //sumatorio del peso de las palabras negativas
		$mods = array(); //modificadores (negación, "muy", etc)
		foreach ($words[0] as $word) {
			$word = $this->tokenize($word); //quitar acentos
			if (!trim($word)) continue; //asegurarse de que es una palabra
			if (isset($this->data[$word])) {
				$obj = $this->data[$word]; //si existe la palabra en el diccionario...
				if ($obj->tipo == 'mod') { //si es un modificador
					//agregar al array de modificadores para futuras palabras
					$mods[] = (object)array('peso' => $this->data[$word]->peso, 'contador' => $this->data[$word]->contador+1);
				}
				else {
					$peso = $obj->peso;
					foreach ($mods as $mod) {
						$peso *= $mod->peso; //aplicar al peso los modificadores
					}
					if ($obj->tipo == 'pos') $pos += $peso;
					elseif ($obj->tipo == 'neg') $neg += $peso;
					else throw new Exception('Valor incorrecto para opinion ',print_r($obj, true));
				}
			}
			
			//actualizar valores de los modificadores
			$aux = array();
			foreach ($mods as $mod) {
				$mod->contador--;
				if ($mod->contador > 0) $aux[] = $mod;
			}
			$mods = $aux;
		}
		$diff = abs($pos-$neg); //diferencia entre positivo y negativo
		$max = $pos > $neg ? $pos : $neg; //obtener mayor valor
		if ($diff <= $max*self::$MIN_DIFF) return self::NEUTRAL; //si no cumple el umbral es neutral
		return $pos > $neg ? self::POSITIVO : self::NEGATIVO;
	}
	
	/**
	 * Carga el archivo de diccionario
	 * @param string $file Ruta absoluta hacia el archivo
	 */
	private function cargarDiccionario($file) {
		$data = file_get_contents($file);
		$data = explode("\n", $data);
		foreach ($data as $line) {
			if (!$line) continue;
			$line = explode("\t", $line);
			$word = $this->tokenize($line[0]);
			$this->data[$word] = (object)array('tipo' => $line[2], 'peso' => $line[1]);
			if ($line[2] == 'mod') $this->data[$word]->contador = $line[3];
		}
	}
	
	/**
	 * Quita los acentos de un string
	 * @param string $word texto a convertir
	 * @return string texto modificador
	 */
	private function tokenize($word) {
		return strtolower(strtr($word, self::$TOKENIZE_FROM, self::$TOKENIZE_TO));
	}
}