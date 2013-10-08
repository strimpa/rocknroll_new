<?php

	$language = NULL;
	if(isset($_GET['lang']))
	{
		$language = $_GET['lang'];
	}
	else if (isset($_COOKIE['etaf']['lang'])) 
	{
		$language = $_COOKIE['etaf']['lang'];
	}
	else
	{
		$langKeys = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$langKeyArray = explode(",", $langKeys);
		$primaryLanguage = substr($langKeyArray[0], 0, 2);
		switch($primaryLanguage)
		{
			case "en":
				$language = "en";
				break;
			case "es":
				$language = "es";
				break;
			case "de":
				$language = "de";
				break;
			default:
				$language = "cat";
				break;
		}
	}	
	setcookie("etaf[lang]", $language);
	
	require_once("lang/".$language.".php");
?>