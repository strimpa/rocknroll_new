<?php

	$language = NULL;
	if(isset($_GET['lang']))
	{
		$language = $_GET['lang'];
	}
	else if (isset($_COOKIE['rnr']['lang'])) 
	{
		$language = $_COOKIE['rnr']['lang'];
	}
	else
	{
		$langKeys = "en";
		if(array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER))
			$langKeys = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		
		$langKeyArray = explode(",", $langKeys);
		$primaryLanguage = substr($langKeyArray[0], 0, 2);
		switch($primaryLanguage)
		{
			case "de":
				$language = "de";
				break;
			default:
				$language = "en";
				break;
		}
	}	
	setcookie("rnr[lang]", $language);
	
	require_once("lang/".$language.".php");
?>