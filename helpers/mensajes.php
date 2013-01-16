<?php

class Mensaje {
	var $tipo;
	var $msg;
	
	function __construct($tipo, $msg) {
		$this->tipo = $tipo;
		$this->msg = $msg;
	}
}

class Mensajes {
	static $mensajes;
	static function getMensajes() {
		if (!is_array(self::$mensajes)) {
			self::$mensajes = Session::load('mensajes', array());
			Session::save('mensajes', array());
		}
		return self::$mensajes;
	}
	static function addMensaje($tipo, $msg) {
		$m = new Mensaje($tipo, $msg);
		$msgs = Session::load('mensajes', array());
		$msgs[] = $m;
		Session::save('mensajes', $msgs, true);
	}
}

Mensajes::getMensajes();