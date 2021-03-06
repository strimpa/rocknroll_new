<?php
//include("cgi-bin/Drawing.php");
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
		if(array_key_exists("image", $dataAssoc))
			$this->picUrl = $dataAssoc["image"];
		else
			$this->picUrl = null;
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
		$builder = ContentMgr::GetInst()->GetBuilder();

		$div = $builder->AddTag("div", "paragraph_".MakeSafeString($this->header), "paragraph");
//		$builder->AddStyle($div, $this->CreateStyleString($currentOffset));

		$title = null;
		if(""!=$this->header)
		{
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle paragraphTitleText", $this->header);
			$div->appendChild($title);
			$upLink = $builder->AddTag("a", null, "paragraphTitleUpLink", "Nach oben");
			$upLink->setAttribute("href", "#");
			$title->appendChild($upLink);
		}
/*
		else
		{
			$className = "paragraphTitle paragraphDeko";
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), $className);
		}

 * /
/*
		// $newPicUrl = Drawing::RandRect(0);	
		// $newPicUrl = Drawing::RandRect(1);	
		// $newPicUrl = Drawing::RandRect(2);	
		// $newPicUrl = Drawing::RandRect(3);	
		// $newPicUrl = Drawing::RandRect(4);	
		// $newPicUrl = Drawing::RandRect(5);	
		// $newPicUrl = Drawing::RandRect(6);	
		// $newPicUrl = Drawing::RandRect(7);	
		// $newPicUrl = Drawing::RandRect(8);	
		// $newPicUrl = Drawing::RandRect(9);	
		// PrintHtmlComment("newPicUrl:$newPicUrl");		
//		$testPic = $builder->GetDoc()->createElement("img");
//		$testPic->setAttribute("src", $newPicUrl);
//		$div->appendChild($testPic);	
 * 
 * 
 */	
		$content = $builder->AddTag("div", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		$contentHeight = ($this->height-iParagraph::TITLE_HEIGHT);
//		$builder->AddStyle($content, "height:".$contentHeight."px;");
		$text = $builder->AddTag("p", "paragraph_content_".MakeSafeString($this->header), "paragraphText");
		if(null!=$this->picUrl)
		{
			$pic = $builder->AddImage($this->picUrl, $this->picAlign, true);
			$content->appendChild($pic);
		}
		$content->appendChild($text);
		
		if($this->content!="")
		{
			$importdoc = new DOMDocument();
			$importdoc->encoding = 'UTF-8';
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
		
//		$content->setAttribute("style", "height:".($this->height-40)."px;");
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
	private $sortBy;
	private $flags;
	private $eventTypePicPath = "/images/layout/";
	
	const TABLE_CULL_BY_DATE = 1;

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
		$this->sortBy = isset($dataAssoc["sortBy"]) ? $dataAssoc["sortBy"] : null;
		$this->flags = isset($dataAssoc["flags"]) ? $dataAssoc["flags"] : 1;
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
		$success = true;
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
						$value = trim($resultArray[$text]);
						if(""==$value && $currRoot->tagName=="div")
						{
							$myElement = NULL;
						}
						else if($type=="date")
						{
							$date = new DateTime($value);
							$weekday = strftime("%A", strtotime($value));//$date->getTimestamp());
							$value = substr($weekday, 0, 2).", ".$date->format('d.m.');
//							$print ($weekday);
//							$lastSlashIndex = strrpos($value, "-");
//							$value = substr($value, $lastSlashIndex+1);
							$myElement->nodeValue = $value;
						}
						else if($type=="geo")
						{
							$myElement->nodeValue = $value;
						}
						else if($type=="pic" && $value!="")
						{
							$picLink = $doc->createElement("a");
							$pic = $doc->createElement("img");
							//$index = intval($value);
							PrintHtmlComment($value);
							//$pic->setAttribute("src", $this->eventTypePicPath.$this->eventTypePics[$index]);
							$pic->setAttribute("src", $value);
							$pic->setAttribute("class", "eventPic");
							
							$picLink->appendChild($pic);
							$picLink->setAttribute("href", $value);
							$picLink->setAttribute("target", "_blank");
							$picLink->setAttribute("rel", "lightbox[eventPic]");
							$myElement->appendChild($picLink);
							
							$hasPicAttached = true;
						}
						else if($type=="url")
						{
							$picLink = $doc->createElement("a");
							$wwwDiv = $doc->createElement("div");
							$wwwDiv->appendChild($doc->createTextNode("www"));
							$picLink->appendChild($wwwDiv);
							$picLink->setAttribute("href", $value);
							$picLink->setAttribute("target", "_blank");
							$myElement->appendChild($picLink);
						}
						else if($type=="mail")
						{
							$picLink = $doc->createElement("a");
							$wwwDiv = $doc->createElement("img");
							$wwwDiv->setAttribute("src", $this->eventTypePicPath."mail.png");
							$picLink->appendChild($wwwDiv);
							$picLink->setAttribute("href", "mailto:".$value);
							$myElement->appendChild($picLink);
						}
						else if($type=="tel")
						{
							$wwwDiv = $doc->createElement("img");
							$wwwDiv->setAttribute("src", $this->eventTypePicPath."telephone.png");
							$myElement->appendChild($wwwDiv);
							$myElement->appendChild($doc->createTextNode($value));
						}
						else if($type=="time")
						{
							$timepieces = explode(":", $value);
							if(!($timepieces[0]=="00" || $timepieces[1]=="00"))
							{
								$myElement->nodeValue = $timepieces[0].":".$timepieces[1]." Uhr";
							}
						}
						else
						{
							$value = EncodeUmlaute($value);
							$myElement->nodeValue = $value;
						}
					}
				}
			}

			$renderMe = true;
			foreach($currRoot->childNodes as $child)
			{
				$childParent = $parent;
				if(NULL!=$myElement)
					$childParent = $myElement;
				if(!$this->RenderXMLNode($doc, $childParent, $child, $resultArray, $hasPicAttached))
				{
					$renderMe = false;
				}
			}
			// only if not empty
			if(null!=$myElement)
			{
				$parent->appendChild($myElement);
			}
		}
		return $success;
	}

	public function GetTableResults()
	{
		$configXML = new DOMDocument();
		$configXML->load($this->tableType.".xml");
		
		$root = $configXML->firstChild;
//		try{
		$dbTable = $root->getAttribute("name");
		$reqArray = array("table"=>$dbTable);
		if(""!=$this->category)
			$reqArray["requirements"] = array("category"=>$this->category);

		$tableDef = DBCntrl::GetInst()->Conn()->GetTableDef($reqArray);
		if(array_key_exists("date", $tableDef[0]))
		{
			$reqArray["orderBy"] = "date";
		}
		else if(array_key_exists("issue", $tableDef[0]))
		{
			$reqArray["orderBy"] = "issue";
		}

		return DBCntrl::GetInst()->Conn()->GetTableContent($reqArray);
	}
	
	public function Render(&$parentNode, &$currentOffset)
	{
		$builder = ContentMgr::GetInst()->GetBuilder();

		$div = $builder->AddTag("div", "paragraph_".MakeSafeString($this->header), "paragraph");
//		$builder->AddStyle($div, $this->CreateStyleString($currentOffset));

		if(""!=$this->header)
		{
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle paragraphTitleText", $this->header);
			$title->setAttribute("align", "right");
			$div->appendChild($title);
			$upLink = $builder->AddTag("a", null, "paragraphTitleUpLink", "Nach oben");
			$upLink->setAttribute("href", "#");
			$title->appendChild($upLink);
		}
		$content = $builder->AddTag("div", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		
		$configXML = new DOMDocument();
		$configXML->load($this->tableType.".xml");
		
		$root = $configXML->firstChild;
//		try{
		$dbTable = $root->getAttribute("name");
		$reqArray = array("table"=>$dbTable);
		$reqArray["requirements"] = array("approved"=>1);
		if(""!=$this->category)
			$reqArray["requirements"]["category"] = $this->category;

		$tableDef = DBCntrl::GetInst()->Conn()->GetTableDef($reqArray);
		if(array_key_exists("date", $tableDef[0]))
		{
			$reqArray["orderBy"] = "date";

			if($this->flags&TablePara::TABLE_CULL_BY_DATE)
			{
				$reqArray["requirements"]["date"] = "NOW()";
			}
		}
		else if(array_key_exists("issue", $tableDef[0]))
		{
			$reqArray["orderBy"] = "issue";
		}
			
		$dbResult = DBCntrl::GetInst()->Conn()->GetTableContent($reqArray);
		//$dbResult = $this->GetTableResults();
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
		
//		$content->setAttribute("style", ("height:".($this->height-40)."px;"));
		
		$currentOffset += ($this->height + iParagraph::PARAGRAPH_PADDING);
	}
}

?>