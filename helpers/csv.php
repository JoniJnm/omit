<?php

class CSV {
	/**
	 * Busca la información de los alumnos estructurados en formado csv
	 * @param string $data los datos de los alumnos en formato csv
	 * @return stdclass[] los datos de los alumnos
	 */
	static function &getAlumnos(&$data) {
		$data = Utils::utf8_encode($data);
		$lines = explode("\n", $data);
		$len = count($lines);
		$users = array();
		if ($len < 14) return $users;
		for ($i=13; $i<$len; $i++) {
			$line = explode(';', $lines[$i]);
			$trim = " \t\n\r\0\x0B ";
			if (count($line) != 17 || !trim($line[7], $trim)) break;
			$users[] = (object)array(
				'apellido1' => trim($line[1], $trim),
				'apellido2' => trim($line[2], $trim),
				'nombre' => trim($line[3], $trim),
				'dni' => trim($line[4], $trim),
				'email' => trim($line[7], $trim)
			);
		}
		return $users;
	}
	
	/**
	 * Pinta en formato csv la información de la universidad (para poder después descargarlo, por ejemplo)
	 * @param stdclass[] $data la información a mostrar en csv
	 */
	static function printUniDataObj(&$data) {
		foreach ($data as $tabla=>$info) {
			echo $tabla."\n";
			$keys = array_keys(get_object_vars($info[0]));
			echo Utils::utf8_decode(implode(";", $keys))."\n";
			foreach ($info as $obj) {
				$values = array_values(get_object_vars($obj));
				echo Utils::utf8_decode(implode(";", $values))."\n";
			}
			echo "\n";
		}
	}
	
	/**
	 * Convierte un archivo cvs subido al servidor a con la información de la universidad
	 * a objeto php con arrays
	 * @param string $name el nombre del valor del formación del archivo
	 * @return array de arrays con la información del csv
	 */
	static function getUniObjFromCSVUploaded($name) {
		$str = @file_get_contents($_FILES[$name]["tmp_name"]);
		$str = Utils::utf8_encode($str);
		$lines = explode("\n", $str);
		$data = array();
		$load_struct = false;
		$last_table = "";
		$struct = array();
		foreach ($lines as $line) {
			$line = trim($line);
			$line = preg_replace("#;+$#", '', $line);
			$cols = explode(";", $line);
			$c = count($cols);
			if ($c <= 0 || !trim($cols[0])) continue;
			if ($c == 1) {
				$load_struct = true;
				$last_table = $cols[0];
				if (!$last_table) die($line);
				$data[$last_table] = array();
			}
			elseif ($load_struct) {
				$struct = $cols;
				$load_struct = false;
			}
			else {
				if (count($struct) != $c || !$last_table) return array();
				$obj = new stdclass;
				for ($i=0; $i<$c; $i++) {
					$obj->$struct[$i] = $cols[$i];
				}
				$data[$last_table][] = $obj;
			}
		}
		return $data;
	}
}
