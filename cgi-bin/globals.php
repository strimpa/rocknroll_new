<?php
	global $build_errors;

	$path_parts = pathinfo(__FILE__);
//	$img = ($path_parts['dirname']."/".$prefix."_".rand(1,8).".png");
	$serverRoot = $_SERVER['DOCUMENT_ROOT']."/rocknroll_new/"; // "/"
	$httpRoot = "http://".$_SERVER['SERVER_NAME']."/rocknroll_new/"; // "/"

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