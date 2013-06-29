<?php

/**
 * Clase para el control de la base de datos MySQL
 * 
 * La configuración de acceso se encuentra en el archivo /init/config.php
 */

class Database {
	/**
	 * Variable con un string de error (si lo hubo) en la última operación MySQL
	 * @var type 
	 */
	public $error = "";
	
	/**
	 *
	 * @var mysqli
	 */
	private $mysqli;
	private $pre;
	
	function __construct($server, $user, $pass, $db, $pre="") {
		$this->mysqli = new mysqli($server, $user, $pass, $db);
		if (mysqli_connect_error()) {
			$this->error = 'Error de Conexión (' . mysqli_connect_errno() . ') '.mysqli_connect_error();
			trigger_error($this->error, E_USER_ERROR);
		}
		$this->mysqli->set_charset('utf8');
		$this->mysqli->query("SET NAMES 'utf8'");
		$this->pre = $pre;
	}
	
	/**
	 * Obtiene la instancia de la clase (para crear un singlentón)
	 * @return Database
	 */
	static function &getInstance() {
		static $class = null;
		if (!is_object($class)) {
			$class = new self(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PREFIX);
		}
		return $class;
	}
	
	/**
	 * Ejecuta una sentencia MySQL
	 * @param string $query Consulta a ejecutar
	 * @return mixed El valor devuelto por mysqli::query()
	 */
	function query($query) {
		if ($this->pre) {
			$query = str_replace("#__", $this->pre, $query);
		}
		$return = $this->mysqli->query($query);
		if (!$return) {
			$this->error = $this->mysqli->error;
			trigger_error("Error en la base de datos: <br />\nQuery: ".$query."<br />\nError: ".$this->error, E_USER_ERROR);
		}
		else {
			$this->error = "";
		}
		return $return;
	}
	
	/**
	 * Carga la primera fila de un select en un objeto PHP
	 * @param string $query
	 * @return sdtclass
	 */
    function loadObject($query){
		$cur = $this->query($query);
        $ret = null;
        if ($object = $cur->fetch_object()) {
            $ret = $object;
        }
        $cur->free();
        return $ret;
    }
	
	/**
	 * Carga el valor de la columna $col de la primera fila en una variable PHP
	 * @param string $query
	 * @param int $col Número de columna (por defecto 0, es decir, la primera)
	 * @return mixed
	 */
    function loadResult($query, $col=0) {
        $cur = $this->query($query);
        $ret = null;
        if ($row = $cur->fetch_row()) {
            $ret = $row[$col];
        }
        $cur->free();
        return $ret;
    }
	
	/**
	 * Carga las filas de respuesta en una lista de objetos PHP
	 * @param string $query
	 * @return stdclass[]
	 */
    function loadObjectList($query) {
        $cur = $this->query($query);
        $array = array();
        while ($row = $cur->fetch_object()) {
            $array[] = $row;
        }
        $cur->free();
        return $array;
    }

	/**
	 * Carga todos los valores de una columna en un array de PHP
	 * @param string $query
	 * @param int $col Número de columna (por defecto 0, es decir, la primera)
	 * @return array
	 */
    function loadResultArray($query, $col = 0) {
		if (!($cur = $this->query($query))) {
			return null;
		}
		$array = array();
		while ($row = $cur->fetch_row()) {
			$array[] = $row[$col];
		}
		$cur->free();
		return $array;
	}
	
	/**
	 * Escapa un valor para poder ser insertado en una sentencia MySQL
	 * Le añade las comillas
	 * @param mixed $str Valor a escapar
	 * @return string valor escapado
	 */
	function scape($str) {
		if (is_numeric($str)) return $str;
		elseif ($str === null) return 'null';
		elseif ($str === false) return 'false';
		elseif ($str === true) return 'true';
		else return "'".$this->mysqli->real_escape_string($str)."'";
	}
}