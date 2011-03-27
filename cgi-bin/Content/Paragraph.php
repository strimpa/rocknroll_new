<?php


interface iParagraph
{
	const eTYPE_PIC_RIGHT=0, eTYPE_PIC_LEFT=1, eTYPE_TABLE=2;
	const PARAGRAPH_PADDING = 15;

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
		if(count($keyValueTuple)>1)
		{
			$dataAssoc[$keyValueTuple[0]] = $keyValueTuple[1];
		}
	}
	return $dataAssoc;
}

class PicPara implements iParagraph
{
	static $ePIC_LEFT = 0, $ePIC_RIGHT = 1;
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

		$title = $builder->AddTag("p", "paragraph_title_".MakeSafeString($this->header), "paragraphTitle", $this->header);
		$div->appendChild($title);
		$content = $builder->AddTag("p", "paragraph_content_".MakeSafeString($this->header), "paragraphContent");
		$pic = $builder->AddImage($this->picUrl, $this->picAlign);
		$div->appendChild($pic);
		$div->appendChild($content);
		
		$importdoc = new DOMDocument();
		$importdoc->loadXML("<balls>".$this->content."</balls>");
		$doc = $builder->GetDoc();
		$text = $doc->importNode($importdoc->firstChild, true);
		
		$content->nodeValue = $text->nodeValue;
//		$builder->AddText($content, $this->content);//DitchQuotes(

		$parentNode->appendChild($div);
		
		$currentOffset += ($this->height + iParagraph::PARAGRAPH_PADDING);
	}
}

?>