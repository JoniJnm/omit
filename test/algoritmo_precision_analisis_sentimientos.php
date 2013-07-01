<?php

/*
Archivo para calcular la precisión del algoritmo de análisis de sentimientos
usando las noticias de cine de https://dl.dropbox.com/u/14097279/AnalysisXML.zip

El algoritmo se puede utilizar con otros textos, sólo hay que modificarlo para obtener
el comentario y el rango.

Para utiliar el algoritmo con las críticas de cine hay que definir la constante FOLDER
con la ruta de la carpeta de los archivos con las críticas de cine.

También hay que incluir la clase Opinion para clasificar la polaridad.

*/

DEFINE("FOLDER", "D:/www/xmls/cine");
require_once(dirname(__FILE__).'/opinion.php');

$handle = opendir(FOLDER);

$data = array();
for ($i=0; $i<3; $i++) {
	$data[$i] = array(0, 0, 0);
}

if (!file_exists(FOLDER) || !is_dir(FOLDER)) {
	die("La carpeta '".FOLDER."' no existe");
}

set_time_limit(0);

while (false !== ($entry = readdir($handle))) {
	if ($entry == '.' || $entry == '..') continue;
	$content = file_get_contents(FOLDER.'/'.$entry);
	preg_match('#<body>(.*?)</body>#', $content, $body);
	$body = $body[1];
	preg_match('# rank="(.*?)" #', $content, $rank1);
	$rank1 = $rank1[1];
	
	if ($rank1 >= 4) $r1 = 0;
	elseif ($rank1 <= 2) $r1 = 1;
	else $r1 = 2;
	
	$rank2 = Opinion::clasificar($body);
	if ($rank2 == Opinion::POSITIVO) $r2 = 0;
	elseif ($rank2 == Opinion::NEGATIVO) $r2 = 1;
	else $r2 = 2;
	
	$data[$r1][$r2]++;
}

?>
<table>
	<?php foreach ($data as $d) : ?>
	<tr>
		<?php foreach ($d as $v) : ?>
		<td><?php echo $v; ?></td>
		<?php endforeach; ?>
	</tr>
	<?php endforeach; ?>
</table>