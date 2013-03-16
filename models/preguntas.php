<?php

class Preguntas {
	static function parsearRespuestas($str) {
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
				return false;
		}
		return $res;
	}
}