<?php
/**
* Diese KLasse beschreibt den Aufenthalt eines Besuchers der Site
 * 
 * PERSISTENCE: SESSION
*/
require_once("IPlugin.php");
require_once("Benutzer.php");
require_once("DBCntrl.php");
require_once("Utils.php");

global $loadingErrors;

class Aufenthalt
{
	private static $instance;
	private $aktuellerNutzer;
	private $plugins;
	
	public $pluginStorage; 

	private function Aufenthalt()
	{
		$this->aktuellerNutzer = new Benutzer();
		$this->pluginStorage = array();
		$testVar = 0;
	}
	
	public static function &GetInst()
	{
		if(!array_key_exists('Aufenthalt', $_SESSION))
		{
			PrintHtmlComment("New Aufenthalf");
			$_SESSION['Aufenthalt'] = new Aufenthalt();
		}
		return $_SESSION['Aufenthalt'];
	}
	
	function &GetUser()
	{
		return $this->aktuellerNutzer;
	}
	
	function &GetPlugins()
	{
		if(true)//NULL==$this->plugins || count($this->plugins)<=0)
		{
			$this->plugins = array();
			$pluginDir    = realpath(dirname(__FILE__))."/Plugins";
			$filesNDirs = scandir($pluginDir);

			foreach ($filesNDirs as $key => $potentialDir) 
			{
				$subDir = $pluginDir."/".$potentialDir."/";
				$mainFile = $subDir."plugin.php";
				$indexFile = $subDir."index.php";
				if(is_dir($subDir) && file_exists($indexFile))
				{
					$this->plugins[$potentialDir] = new DummyPlugin($indexFile);
				}	
				if(is_dir($subDir) && file_exists($mainFile))
				{
					$classes = get_declared_classes();
					include $mainFile;
					$diff = array_diff(get_declared_classes(), $classes);
					$class = reset($diff);
					foreach ($diff as $key => $potentialClass) 
					{
						$interfaces = class_implements($potentialClass);
						PrintHtmlComment("interfaces for $potentialClass in $potentialDir:");
						foreach ($interfaces as $value) {
							PrintHtmlComment($value);
						}
						if(array_key_exists("IPlugin", $interfaces))
						{
							$this->plugins[$potentialDir] = new $potentialClass;
						}
						else 
						{
							PrintHtmlComment("Couldn't get IPLugin class of locally resident index.php for plugin $potentialClass");					
						}
					}
				}
			}
 		}
		return $this->plugins;
	}
}

/**
 *  Including files for certain classes to be taken in to account for session storage. 
 */
$pluginDir    = realpath(dirname(__FILE__))."/Plugins";
$filesNDirs = scandir($pluginDir);
foreach ($filesNDirs as $key => $potentialDir) 
{
	$subDir = $pluginDir."/".$potentialDir."/";
	$includeFile = $subDir."include.php";
	if(is_dir($subDir) && file_exists($includeFile))
	{
//		PrintHtmlComment("including $includeFile");
		include $includeFile;
	}
}
 
?>