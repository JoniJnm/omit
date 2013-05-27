<?php

/**
 * Clase para poder controlar de manera sencilla la variable $_SESSION de PHP
 */

class Session {
	static function get($k, $def='') {
		if (isset($_SESSION[$k])) {
			$v = $_SESSION[$k];
			$out = @unserialize($v);
			return $out !== false || $v == serialize(false) ? $out : $v;
		}
		else
			return $def;
	}
	static function set($k, $v) {
		$_SESSION[$k] = serialize($v);
	}
	static function clear($k) {
		unset($_SESSION[$k]);
	}
}
