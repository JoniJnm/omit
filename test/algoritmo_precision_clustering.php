<?php

/*
Algoritmo para calcular la precisión del algoritmo de clustering
del corpus de noticias de http://www.etsii.urjc.es/~smontalvo/corpus_resources.html#corpus

Se puede utilizar el algoritmo para cualquier tipo de grupos. Sólo hay que modificar
la varible $grupos.

El archivo de entrada debe tener en cada línea el nombre de uno de los archivos.
Para separar los grupos se utiliza un sato de línea. 

Ejemplo de fichero:

grupo1 (puede ser una etiqueta que los junte)
newC00012_001.ES.xml
newC00012_002.ES.xml
20000102_606_C33.source.es

grupo2
19951018-12166_C161.xml.es
newC00005_002.ES.xml
newC00005_003.ES.xml
newC00005_004.ES.xml

*/

DEFINE("FILE", dirname(__FILE__).'/grupos.txt'); //ruta al archivo de grupos

$grupos = array();

$grupos[] = array(
'newC00012_001.ES.xml',
'newC00012_002.ES.xml',
'newC00012_003.ES.xml',
'newC00012_004.ES.xml',
'newC00012_005.ES.xml',
'newC00012_006.ES.xml',
'newC00012_007.ES.xml',
'newC00012_008.ES.xml',
'newC00012_009.ES.xml',
'newC00012_010.ES.xml',
'newC00012_011.ES.xml',
'newC00012_012.ES.xml',
'newC00012_013.ES.xml',
'newC00012_014.ES.xml',
'newC00012_015.ES.xml');

$grupos[] = array(
'20000102_606_C33.source.es',
'20000102_614_C33.source.es',
'20000102_623_C33.source.es',
'20000102_624_C33.source.es',
'20000102_633_C33.source.es',
'20000102_647_C33.source.es',
'20000102_671_C33.source.es');

$grupos[] = array(
'newC00002_001.ES.xml',
'newC00002_002.ES.xml',
'newC00002_003.ES.xml',
'newC00002_004.ES.xml',
'newC00002_005.ES.xml');

$grupos[] = array(
'19950330-21584_C161.xml.es',
'19950401-00196_C161.xml.es',
'19951010-06115_C161.xml.es',
'19951018-12166_C161.xml.es');

$grupos[] = array(
'newC00005_001.ES.xml',
'newC00005_002.ES.xml',
'newC00005_003.ES.xml',
'newC00005_004.ES.xml');

$grupos[] = array(
'newC00018_001.ES.xml',
'newC00018_002.ES.xml',
'newC00018_003.ES.xml');

$validos = array();
foreach ($grupos as $g) {
	foreach ($g as $v) {
		$validos[] = $v;
	}
}

function obtenerMayorGrupo(&$grupo, &$grupos) {
	$per = array();
	for ($i=0; $i<count($grupos); $i++) {
		$total = 0;
		for ($j=0; $j<count($grupo); $j++) {
			if (in_array($grupo[$j], $grupos[$i])) {
				$total++;
			}
		}
		$per[$i] = $total;
	}
	arsort($per);
	foreach ($per as $key=>$value) {
		return $key;
	}
	die("ERROR obtenerMayorGrupo()");
}

function obtenerTP(&$grupo, &$grupos, $gid) {
	$r = 0;
	foreach ($grupo as $v) {
		if (in_array($v, $grupos[$gid])) $r++;
	}
	return $r;
}

function obtenerTN(&$grupo, &$grupos, $gid) {
	$r = 0;
	foreach ($grupos as $key=>$g) {
		if ($key == $gid) continue;
		foreach ($g as $v) {
			if (!in_array($v, $grupo)) $r++;
		}
	}
	return $r;
}

function obtenerFP(&$grupo, &$grupos, $gid) {
	$r = 0;
	foreach ($grupo as $v) {
		if (!in_array($v, $grupos[$gid])) $r++;
	}
	return $r;
}

function obtenerFN(&$grupo, &$grupos, $gid) {
	$r = 0;
	foreach ($grupos[$gid] as $v) {
		if (!in_array($v, $grupo)) $r++;
	}
	return $r;
}

if (!file_exists(FILE)) die("El archivo '".FILE."' no existe");
$content = file_get_contents(FILE);

$lineas = explode("\n", $content);
$grupo = array();
$gs = 0;
$tps = 0;
$tns = 0;
$fps = 0;
$fns = 0;

for ($i=3; $i<count($lineas); $i++) {
	$linea = trim($lineas[$i]);
	if (in_array($linea, $validos)) {
		$grupo[] = $linea;
	}
	elseif ($grupo) {
		$gid = obtenerMayorGrupo($grupo, $grupos);
		$tp = obtenerTP($grupo, $grupos, $gid);
		$tn = obtenerTN($grupo, $grupos, $gid);
		$fp = obtenerFP($grupo, $grupos, $gid);
		$fn = obtenerFN($grupo, $grupos, $gid);
		
		$total = $tp + $tn + $fp + $fn;
		if ($total != count($validos)) {
			die("ERROR \$tp + \$tn + \$fp + \$fn = ".$total);
		}
		$tps += $tp;
		$tns += $tn;
		$fps += $fp;
		$fns += $fn;
		$gs++;
		$grupo = array();
	}
}

echo "grupos: $gs<br />";
echo "tps: $tps<br />";
echo "tns: $tns<br />";
echo "fps: $fps<br />";
echo "fns: $fns<br />";
echo "Precision: ".number_format(($tps+$tns)/($tps+$tns+$fps+$fns), 6);