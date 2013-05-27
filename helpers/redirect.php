<?php

/**
 * Usado para redigir al usuario a una determinada dirección web
 * 
 * Podría ser una función simple en alguna otra clase
 */

class Redirect {
	function _($url) {
		if (substr($url, 0, 4) != 'http')
			$url = HTML_URL.$url;
		header("location: ".$url, true, 301);
		exit;
	}
}