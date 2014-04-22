<?php
	global $build_errors;
	global $serverRoot;

	$path_parts = pathinfo(__FILE__);
	$serverRoot = $_SERVER['DOCUMENT_ROOT']."/";
	$httpRoot = "http://".$_SERVER['SERVER_NAME']."/";

	if($_SERVER['SERVER_NAME']=="localhost")
	{
		$serverRoot .= "rocknroll_new/";
		$httpRoot .= "rocknroll_new/";
	}

	setlocale(LC_ALL, 'de_DE');

	// mysql5.rock-around.de
	$db_serv = 'localhost';//'mysql.tuets.com';
	// MySQL Datenbank Name
	$db_name = 'rocknroll';//'rnr_new';
	// User
	$db_user = 'HR'; //db85283
	// Passwort
	$db_pass = 'hr';//'HoexNumi';
?>