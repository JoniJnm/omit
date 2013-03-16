<?php

class Database {
	private $mysqli;
	private $pre;
	var $error = "";
	
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
	 * 
	 * @return Database
	 */
	static function &getInstance() {
		static $class = null;
		if (!is_object($class)) {
			$class = new self(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PREFIX);
		}
		return $class;
	}
	
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
	
    function loadObject($query){
		$cur = $this->query($query);
        $ret = null;
        if ($object = $cur->fetch_object()) {
            $ret = $object;
        }
        $cur->free();
        return $ret;
    }
	
    function loadResult($query, $col=0) {
        $cur = $this->query($query);
        $ret = null;
        if ($row = $cur->fetch_row()) {
            $ret = $row[$col];
        }
        $cur->free();
        return $ret;
    }
	
    function loadObjectList($query) {
        $cur = $this->query($query);
        $array = array();
        while ($row = $cur->fetch_object()) {
            $array[] = $row;
        }
        $cur->free();
        return $array;
    }

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
	
	function scape($str) {
		return "'".$this->mysqli->real_escape_string($str)."'";
	}
}