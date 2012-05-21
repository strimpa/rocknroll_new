<?php
include("cgi-bin/Drawing.php");
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
		$builder = ContentMgr::GetInstance()->GetBuilder();

		$div = $builder->AddTag("div", "paragraph_".MakeSafeString($this->header), "paragraph");
		$builder->AddStyle($div, $this->CreateStyleString($currentOffset));

		$title = null;
		if(""!=$this->header)
		{
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle paragraphTitleText", $this->header);
		}
		else
		{
			$className = "paragraphTitle paragraphDeko";
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), $className);
		}
		$div->appendChild($title);
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
		$content = $builder->AddTag("div", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		$contentHeight = ($this->height-iParagraph::TITLE_HEIGHT);
		$builder->AddStyle($content, "height:".$contentHeight."px;");
		$text = $builder->AddTag("p", "paragraph_content_".MakeSafeString($this->header), "paragraphText");
		if(null!=$this->picUrl)
		{			$pic = $builder->AddImage($this->picUrl, $this->picAlign);
			$content->appendChild($pic);
		}
		$content->appendChild($text);
		
		if($this->content!="")
		{
			$importdoc = new DOMDocument();
			$importdoc->encoding = 'UTF-8';
//			PrintHtmlComment("content:$this->content");
//			PrintHtmlComment("Xml string before import:".$this->content);
			$importdoc->loadHTML('<?xml encoding="UTF-8">\n'.$this->content);
//			PrintHtmlComment("Xml string after import:".$importdoc->C14N());
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
//			PrintHtmlComment("Xml string after welding:".$doc->saveXML($content));
		}
		$div->appendChild($content);
		$parentNode->appendChild($div);
		
		$content->setAttribute("style", "height:".($this->height-40)."px;");
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
							$date = new DateTime($value);
							$weekday = strftime("%A", strtotime($value));//$date->getTimestamp());
							$value = substr($weekday, 0, 3).",".$date->format('d.m.');
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
			$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle paragraphTitleText", $this->header);
			$title->setAttribute("align", "right");
			$div->appendChild($title);
		}
		$content = $builder->AddTag("div", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		
		$configXML = new DOMDocument();
		$configXML->load($this->tableType.".xml");
		
		$root = $configXML->firstChild;
//		try{
		$dbTable = $root->getAttribute("name");
		$dbResult = Aufenthalt::GetInstance()->DBConn()->GetTableContent(
			array(
				"table"=>$dbTable, 
				"requirements"=>array("category"=>$this->category)
				));
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
		
		$content->setAttribute("style", ("height:".($this->height-40)."px;"));
		
		$currentOffset += ($this->height + iParagraph::PARAGRAPH_PADDING);
	}
}
?>