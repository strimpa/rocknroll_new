<?php

/**
 *  class to 
 */

class GalleryPlugin implements IPlugin
{
	public function GetType()
	{
		return ePLUGTYPE_ARTICLE;
	}
	public function GetPath()
	{
		return realpath(dirname(__FILE__));
	}
	public function GetName()
	{
		$path_parts = pathinfo(__FILE__);
		return basename($path_parts['dirname']);
	}
	public function GetAdminScript()
	{
		return $this->GetName()."/js/admin";
	}
	
	public function RenderHeader()
	{
		$path = PLUGIN_SUBDIR."/".$this->GetName();
print <<<EOD
	<link rel="stylesheet" href="$path/css/mediaboxAdvWhite.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="$path/css/Gallery.css" type="text/css" media="screen" />
	<script src="$path/js/mootools.js" type="text/javascript"></script>
	<script src="$path/js/mediaboxAdv-1.3.4b.js" type="text/javascript"></script>
EOD;
	}
	public function RenderContent(&$parentNode)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		ob_start();
		chdir($this->GetPath());
		include "index.php";
		$str=ob_get_contents();
		ob_end_clean();	
		$importdoc = new DOMDocument();
		$importdoc->encoding = 'UTF-8';
		$importdoc->loadHTML('<?xml encoding="UTF-8">'.$str);
		$doc = $builder->GetDoc();
		$text = $doc->importNode($importdoc->documentElement, true);
		$parentNode->appendChild(new DOMComment("Begin import"));
		$parentNode->appendChild($text);
	}
}

?>