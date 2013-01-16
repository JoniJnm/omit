<?php

class database {
	private $conect;
	private $pre;
	var $error = "";
	
	function __construct($server, $user, $pass, $db, $pre="") {
		$this->conect = mysql_connect($server, $user, $pass);
		if (!$this->conect) {
			$this->error = mysql_error();
			trigger_error("Error en la base de datos: <br />\n".$this->error, E_USER_ERROR);
		} 
		else {
			$this->error = "";
		}
		mysql_select_db($db, $this->conect);
		mysql_query("SET NAMES 'utf8'");
		$this->pre = $pre;
	}
	
	function &getInstance() {
		static $class;
		if (!is_object($class)) {
			$config =& config::getInstance();
			$class = new database($config->server, $config->user, $config->pass, $config->db, $config->pre);
		}
		return $class;
	}
	
	function query($query) {
		if ($this->pre) {
			$query = str_replace("#__", $this->pre, $query);
		}
		$return = mysql_query($query, $this->conect);
		if (!$return) {
			$this->error = mysql_error($this->conect);
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
        if ($object = mysql_fetch_object($cur)) {
            $ret = $object;
        }
        mysql_free_result($cur);
        return $ret;
    }
	
    function loadResult($query) {
        $cur = $this->query($query);
        $ret = null;
        if ($row = mysql_fetch_row($cur)) {
            $ret = $row[0];
        }
        mysql_free_result($cur);
        return $ret;
    }
	
    function loadObjectList($query) {
        $cur = $this->query($query);
        $array = array();
        while ($row = mysql_fetch_object($cur)) {
            $array[] = $row;
        }
        mysql_free_result($cur);
        return $array;
    }

    function loadResultArray($query, $numinarray = 0) {
		if (!($cur = $this->query($query))) {
			return null;
		}
		$array = array();
		while ($row = mysql_fetch_row( $cur )) {
			$array[] = $row[$numinarray];
		}
		mysql_free_result( $cur );
		return $array;
	}
}