<?php

require_once(dirname(dirname(__file__)).'/config.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
header("Content-type: application/json; charset=UTF-8");

$out = array();

$db = Database::getInstance();
$task = get('task');
if ($task == 'getData') {
	$data = getData();
	$out['data'] = array();
	foreach ($data as $key=>$value) {
		$out['data'][$key] = $value;
	}
}
echo json_encode($out);