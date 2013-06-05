<?php

class Utils {
	/**
	 * Redirige al usuario a la dirección web deseada
	 * @param string $url La url
	 */
	static function redirect($url) {
		if (substr($url, 0, 4) != 'http')
			$url = HTML_URL.$url;
		header("location: ".$url, true, 301);
		exit;
	}
	
	/**
	 * Si un string no está codificado como UTF-8, lo codifica y lo devuelve
	 * @param string $str La cadena de texto a codificar en UTF-8
	 * @return string la cadena en utf-8
	 */
	static function utf8_encode($str) {
		return mb_detect_encoding($str, "UTF-8", true) === false ? utf8_encode($str) : $str;
	}
	
	/**
	 * Si un string está codificado como UTF-8, lo decodifica y lo devuelve
	 * @param string $str La cadena de texto a decodificar de UTF-8
	 * @return string la cadena de texto decodificada
	 */
	static function utf8_decode($str) {
		return mb_detect_encoding($str, "UTF-8", true) === false ? $str : utf8_decode($str);
	}
}
