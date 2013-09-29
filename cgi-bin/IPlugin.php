<?php

const PLUGIN_SUBDIR = "/cgi-bin/Plugins";
const SCRIPT_PLUGIN_SUBDIR = "/cgi-bin/Plugins";

interface IPlugin
{
	const ePLUGTYPE_NONE=0,ePLUGTYPE_ARTICLE=1, ePLUGTYPE_PARAGRAPH=2;
	
	public function GetType();
	public function GetPath();
	public function GetName();
	public function GetAdminScript();
	public function RenderHeader();
	public function RenderContent(&$parentNode);
}

class DummyPlugin implements IPlugin
{
	private $path;
	function DummyPlugin($path)
	{
		$this->path = $path;
	}
	public function GetType()
	{
		return ePLUGTYPE_ARTICLE;
	}
	public function GetPath()
	{
		return $this->path;
	}
	public function GetName()
	{
		$path_parts = pathinfo($this->path);
		return basename($path_parts['dirname']);
	}
	public function RenderHeader()
	{
		
	}
	public function GetAdminScript()
	{
		return $this->GetName()."/admin";
	}
	public function RenderContent(&$parentNode)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();
		ob_start();
		include $this->path;
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