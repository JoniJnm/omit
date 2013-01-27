<?php

Class Request {
	static function get($k, $def='') {
		return isset($_GET[$k]) ? $_GET[$k] : $def;
	}
	static function post($k, $def='') {
		return isset($_POST[$k]) ? $_POST[$k] : $def;
	}
	static function both($k, $def='') {
		return self::get($k, self::post($k, $def));
	}
}
