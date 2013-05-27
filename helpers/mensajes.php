<?php

/**
 * Clase para controlar los mensajes a mostrar en la página web.
 * Se mostrarán en la parte superior. 
 * Se guardan en la sesión del usuario.
 * Cada mensaje sólo se mostrará una vez.
 */

class Mensajes {
	static private $mensajes;
	/**
	 * Obtiene la lista de mensajes que deben mostrarse al usuario.
	 * Cuando se llama a esta función los mensajes son borrados.
	 * @return stdclass[] Lista de mensajes. 
	 *		El objeto de cada array tiene:
	 *		· tipo: info o alerta
	 *		  msg: el texto del mensaje
	 */
	static function getMensajes() {
		if (!is_array(self::$mensajes)) {
			self::$mensajes = Session::get('mensajes', array());
			Session::set('mensajes', array());
		}
		return self::$mensajes;
	}
	
	/**
	 * Añade un mensaje de tipo alerta
	 * Esta función debe usarse antes de que haya ningún tipo de salida html
	 * @param string $msg
	 */
	static function addAlerta($msg) {
		self::addMensaje('alerta', $msg);
	}
	
	/**
	 * Añade un mensaje de tipo información
	 * Esta función debe usarse antes de que haya ningún tipo de salida html
	 * @param string $msg
	 */
	static function addInfo($msg) {
		self::addMensaje('info', $msg);
	}
	
	/**
	 * Añade un mensaje
	 * @param string $tipo Tipo de mensaje (alerta o info)
	 * @param string $msg contenido del mensaje
	 */
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