<?php

	$path_parts = pathinfo(__FILE__);
	//$serverRoot = $path_parts['dirname']."/";//"/rocknroll_new/";
	$serverRoot = $_SERVER['DOCUMENT_ROOT']."/rocknroll_new/";
	$httpRoot = "http://".$_SERVER['SERVER_NAME']."/rocknroll_new/";

	setlocale(LC_ALL, 'de_DE');

	// mysql5.rock-around.de
	$db_serv = 'localhost';//'mysql5.rock-around.de';
	// MySQL Datenbank Name
	$db_name = 'rocknroll';//'db85283_2';
	// User
	// Domaingo: db85283_2
	$db_user = 'HR';
	// Passwort
	$db_pass = 'hr';//'HoexNumi';
?>