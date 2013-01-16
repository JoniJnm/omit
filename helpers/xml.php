<?php

function getIndividual($v) {
	$data = array(
		'titulaciones' => 'titulacion',
		'cursos' => 'curso',
		'asignaturas' => 'asignatura',
		'profesores' => 'profesor',
		'profesores_asignaturas' => 'profesor_asignatura'
	);
	return $data[$v];
}

function printXML($data) {
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	echo '<data>'."\n";
	foreach ($data as $k=>$v) {
		echo '	<'.$k.'>'."\n";
		foreach ($v as $d) {
			echo '		<'.getIndividual($k).'>'."\n";
			if ($k == 'profesores_asignaturas') {
				echo '			<profesor>'.$d->asignatura.'</profesor>'."\n";
				echo '			<asignatura>'.$d->asignatura.'</asignatura>'."\n";
			}
			else {
				echo '			<id>'.$d->id.'</id>'."\n";
				echo '			<nombre>'.$d->nombre.'</nombre>'."\n";
				if ($k == 'cursos')
					echo '			<titulacion>'.$d->titulacion.'</titulacion>'."\n";
				elseif ($k == 'asignaturas')
					echo '			<curso>'.$d->curso.'</curso>'."\n";
			}
			echo '		</'.getIndividual($k).'>'."\n";
		}
		echo '	</'.$k.'>'."\n";
	}
	echo '</data>'."\n";
}

function getXMLData($name) {
	if (isset($_FILES[$name]["tmp_name"]))
		return json_decode(json_encode(simplexml_load_file($_FILES[$name]["tmp_name"])), true);
}