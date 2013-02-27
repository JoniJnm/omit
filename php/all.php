<?php

session_start();

function load($f, $once=true) {
	$f = str_replace('.', '/', $f);
	$f = $f.'.php';
	
	if (file_exists(PHP_PATH.$f)) {
		if ($once) require_once(PHP_PATH.$f);
		else require(PHP_PATH.$f);
	}
	else {
		trigger_error("No se ha podido cargar el archivo '".$f."'", E_USER);
		exit;
	}
}

load('helpers.session');
load('helpers.request');
load('helpers.mensajes');
load("helpers.database");
load("helpers.redirect");
load("helpers.user");