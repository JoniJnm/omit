<?php

class XML {
	/**
	 * Convierte un xml subido a un objeto php de tipo array de arrays con la información del xml
	 * @param string $name el nombre del valor del formación del archivo
	 * @return array de arrays con la información del xml
	 */
	static function getXMLFromFileUploaded($name) {
		if (isset($_FILES[$name]["tmp_name"]))
			return json_decode(json_encode(simplexml_load_file($_FILES[$name]["tmp_name"])), true);
		return false;
	}
	
	/**
	 * Mustra en un xml la información de la universidad (para poder después descargarlo, por ejemplo)
	 * @param stdclass[] $data la información a mostrar en xml
	 */
	static function printUniDataObj($data) {
		echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		echo '<data>'."\n";
		foreach ($data as $k=>$v) {
			echo '	<'.$k.'>'."\n";
			foreach ($v as $d) {
				echo '		<'.self::getIndividual($k).'>'."\n";
				foreach ($d as $key=>$val) {
					echo '			<'.$key.'>'.$val.'</'.$key.'>'."\n";
				}
				echo '		</'.self::getIndividual($k).'>'."\n";
			}
			echo '	</'.$k.'>'."\n";
		}
		echo '</data>'."\n";
	}
	
	/**
	 * Función auxiliar pasa a singular un atributo de datos de la universidad para el xml
	 * @param string $v
	 * @return string
	 */
	static private function getIndividual($v) {
		$data = array(
			'titulaciones' => 'titulacion',
			'cursos' => 'curso',
			'asignaturas' => 'asignatura',
			'usuarios' => 'usuario',
			'usuarios_asignaturas' => 'usuario_asignatura'
		);
		return $data[$v];
	}
}