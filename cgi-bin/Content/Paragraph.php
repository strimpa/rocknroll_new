<?php


interface iParagraph
{
	const eTYPE_PIC_RIGHT=0, eTYPE_PIC_LEFT=1, eTYPE_TABLE=2, eTYPE_ORDER=3;
	const PARAGRAPH_PADDING = 15;
	const TITLE_HEIGHT = 60;
	
	public function InterpreteMetaData($data);
	
	public function Init($header, $meta, $content);
	public function Render(&$parentNode, &$currentOffset);
}

function MetaString2Assoc($data)
{
	$dataArray = explode(";", $data);
	$dataAssoc = array();
	foreach($dataArray as $dataTuple)
	{
		$keyValueTuple = explode("=", $dataTuple);
		PrintHtmlComment($dataTuple);
		if(count($keyValueTuple)>1)
		{
			$dataAssoc[$keyValueTuple[0]] = $keyValueTuple[1];
		}
	}
	return $dataAssoc;
}

class PicPara implements iParagraph
{
//	static $ePIC_LEFT = 0, $ePIC_RIGHT = 1;
	private $header;
	private $height;
	private $picAlign;
	private $picUrl;
	private $content;

	public function PicPara($align)
	{
		$this->picAlign = $align;
	}

	public function InterpreteMetaData($data)
	{
		$dataAssoc = MetaString2Assoc($data);
		$this->height = $dataAssoc["height"];
		$this->picUrl = $dataAssoc["image"];
	}
	
	private function CreateStyleString($offset)
	{
		$styleString = "";
		$styleString .= "top:".$offset."px;";
		$styleString .= "height:".$this->height."px;";
		return $styleString;
	}
	
	public function Init($header, $meta, $content)
	{
		$this->header = $header;
		$this->InterpreteMetaData($meta);
		$this->content = $content;
	}

	public function Render(&$parentNode, &$currentOffset)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();

		$div = $builder->AddTag("div", "paragraph_".MakeSafeString($this->header), "paragraph");
		$builder->AddStyle($div, $this->CreateStyleString($currentOffset));

		if(""!=$this->header)
		{
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle", $this->header);
			$div->appendChild($title);
		}
		
		$content = $builder->AddTag("div", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		$contentHeight = ($this->height-iParagraph::TITLE_HEIGHT);
		$builder->AddStyle($content, "height:".$contentHeight."px;");
		$text = $builder->AddTag("p", "paragraph_content_".MakeSafeString($this->header), "paragraphText");
		$pic = $builder->AddImage($this->picUrl, $this->picAlign);
		$content->appendChild($pic);
		$content->appendChild($text);
		
		if($this->content!="")
		{
			$importdoc = new DOMDocument();
			$importdoc->encoding = 'UTF-8';
//			PrintHtmlComment("content:$this->content");
			$importdoc->loadHTML('<?xml encoding="UTF-8">\n'.$this->content);
			$doc = $builder->GetDoc();
			
			$node = $importdoc->getElementsByTagName("div")->item(0);
			$text = FALSE;
			if(null!=$node)
				$text = $doc->importNode($node, true);
			if(FALSE!=$text)
				$content->appendChild($text);
			else
			{
				$text = $doc->createTextNode("Fehler beim Text laden!");
				$content->appendChild($text);
			}
		}
		$div->appendChild($content);
		$parentNode->appendChild($div);
		
		$currentOffset += ($this->height + iParagraph::PARAGRAPH_PADDING);
	}
}

class TablePara implements iParagraph
{
	private $header;
	private $height;
	private $picAlign;
	private $tableType; 
	private $category;
	private $eventTypePicPath = "/images/images/";
	private $eventTypePics = array("Elements_12.png","Elements_12.png","Elements_12.png","Elements_12.png");

	public function PicPara($align)
	{
		$this->picAlign = $align;
	}

	public function InterpreteMetaData($data)
	{
		$dataAssoc = MetaString2Assoc($data);
		$this->height = $dataAssoc["height"];
		$this->tableType = $dataAssoc["table"];
		$this->category = $dataAssoc["category"];
	}
	
	private function CreateStyleString($offset)
	{
		$styleString = "";
		$styleString .= "top:".$offset."px;";
		$styleString .= "height:".$this->height."px;";
		return $styleString;
	}
	
	public function Init($header, $meta, $content)
	{
		$this->header = $header;
		$this->InterpreteMetaData($meta);
	}
	
	public function RenderXMLNode(&$doc, &$parent, &$currRoot, $resultArray, &$hasPicAttached)
	{
		$myElement = NULL;
		if(is_a($currRoot, "DOMElement"))
		{
			if($currRoot->tagName=="row")
			{
				$myElement = $doc->createElement("tr");
				$myClasses = "tableRow eventRow";
			}
			else if($currRoot->tagName=="data")
			{
				$myElement = $doc->createElement("td");
				$myClasses = "tableData eventData";
			}
			else if($currRoot->tagName=="div")
			{
				$myElement = $doc->createElement("div");
				$myClasses = "tableInnerDiv eventInnerDiv";
			}
			
			if(null!=$myElement)
			{
				//class
				$presetClass = $currRoot->getAttribute("class");
				if(isset($presetClass) && $presetClass!="")
					$myClasses.=" ".$presetClass;
				$myElement->setAttribute("class", $myClasses);
				
				// spans
				$rs = $currRoot->getAttribute("rowspan");
				if(isset($rs) && $rs!="")
					$myElement->setAttribute("rowspan", $rs);
				$cs = $currRoot->getAttribute("colspan");
				if(isset($cs) && $cs!="")
					$myElement->setAttribute("colspan", $cs);

				// randoms
				if(!$hasPicAttached && preg_match("/eventShowMore/", $presetClass))
				{
					$infoElement = $doc->createElement("a");
					$infoElement->nodeValue = "More info";
//					$infoElement->setAttribute("href", "");
					$infoElement->setAttribute("class", "infoDiv");
					$myElement->appendChild($infoElement);
				}
					
				$type = $currRoot->getAttribute("type");
					
				// nodevalue
				$presetKey = $currRoot->firstChild;
				if(is_a($presetKey, "DOMText"))
				{
					$text = $presetKey->wholeText;
					$text = preg_replace("/[^\w]/", "", $text);
					if($text!="")
					{
						$value = $resultArray[$text];
						if($type=="date")
						{
							$lastSlashIndex = strrpos($value, "-");
							$value = substr($value, $lastSlashIndex+1);
							$myElement->nodeValue = $value;
						}
						else if($type=="geo")
						{
							$myElement->nodeValue = $value;
						}
						else if($type=="pic" && $value!="")
						{
							$pic = $doc->createElement("img");
							//$index = intval($value);
							PrintHtmlComment($value);
							//$pic->setAttribute("src", $this->eventTypePicPath.$this->eventTypePics[$index]);
							$pic->setAttribute("src", $value);
							$pic->setAttribute("class", "eventPic");
							$myElement->appendChild($pic);
							
							$hasPicAttached = true;
						}
						else
						{
							$myElement->nodeValue = $value;
						}
					}
				}				
				$parent->appendChild($myElement);
			}
			foreach($currRoot->childNodes as $child)
			{
				$childParent = $parent;
				if(NULL!=$myElement)
					$childParent = $myElement;
				$this->RenderXMLNode($doc, $childParent, $child, $resultArray, $hasPicAttached);
			}
		}
	}
	
	public function Render(&$parentNode, &$currentOffset)
	{
		$builder = ContentMgr::GetInstance()->GetBuilder();

		$div = $builder->AddTag("div", "paragraph_".MakeSafeString($this->header), "paragraph");
		$builder->AddStyle($div, $this->CreateStyleString($currentOffset));

		if(""!=$this->header)
		{
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle", $this->header);
			$div->appendChild($title);
		}
		$content = $builder->AddTag("p", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		
		$configXML = new DOMDocument();
		$configXML->load($this->tableType.".xml");
		
		$root = $configXML->firstChild;
//		try{
		$dbTable = $root->getAttribute("name");
		$dbConn = Aufenthalt::GetInstance()->GetConn();
		$dbResult = $dbConn->GetTableContent($dbTable, "*", array("category"=>$this->category));
		$doc = $builder->GetDoc();
		
		$table = $doc->createElement("table");
		$table->setAttribute("table-layout", "fixed");
		$table->setAttribute("class", "contentTable");
		foreach($dbResult as $data)
		{
			$hasPicAttached = false;
			$this->RenderXMLNode($doc, $table, $root, $data, $hasPicAttached);
		}
		
//		}catch ($e)
//		{
//			PrintHtmlComment("error loading table config.");
//		}
		$content->appendChild($table);
		$div->appendChild($content);
		$parentNode->appendChild($div);
		
		$currentOffset += ($this->height + iParagraph::PARAGRAPH_PADDING);
	}
}

class OrderPara implements iParagraph
{
	public function InterpreteMetaData($data)
	{}
	
	public function Init($header, $meta, $content)
	{}

	public function Render(&$parentNode, &$currentOffset)
	{
		Aufenthalt::GetInstance()->GetAblauf()->aktuellerBestellSchritt($parentNode);
	}
}
?>