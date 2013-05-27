<?php

class Preguntas {
	/**
	 * Parsea un string en busca de valoraciones.
	 * El string separa por ; cada id_pregunta:valor_respuesta (el valor de respuesta va de 1 a 5)
	 * @param string $str El string a parsear
	 * @return string[] cada valor del id de salida será un string con id_pregunta:valor_respuesta
	 */
	static function parsearValoraciones($str) {
		if (!$str) return array();
		$respuestas = explode(';', $str);
		$res = array();
		foreach ($respuestas as $r) {
			$r = explode(':', $r);
			if (count($r) != 2) exit;
			$p = intval($r[0]);
			$v = intval($r[1]);
			if ($p > 0 && $v >= 1 && $v <= 5)
				$res[] = $p.':'.$v;
			else
				return array(); //el string de entrada está mal formado
		}
		return $res;
	}
}