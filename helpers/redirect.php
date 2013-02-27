<?php

class Redirect {
	function _($url) {
		if (substr($url, 0, 4) != 'http')
			$url = HTML_URL.$url;
		header("location: ".$url, true, 301);
		exit;
	}
}