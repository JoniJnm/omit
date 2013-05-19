<?php

class Mensajes {
	static $mensajes;
	static function getMensajes() {
		if (!is_array(self::$mensajes)) {
			self::$mensajes = Session::get('mensajes', array());
			Session::set('mensajes', array());
		}
		return self::$mensajes;
	}
	static function addAlerta($msg) {
		self::addMensaje('alerta', $msg);
	}
	static function addInfo($msg) {
		self::addMensaje('info', $msg);
	}
	static private function addMensaje($tipo, $msg) {
		$m = new stdclass;
		$m->tipo = $tipo;
		$m->msg = $msg;
		$msgs = Session::get('mensajes', array());
		$msgs[] = $m;
		Session::set('mensajes', $msgs, true);
	}
}

Mensajes::getMensajes();