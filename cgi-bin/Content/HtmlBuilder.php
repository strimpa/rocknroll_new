<?php

include("MenuTuple.php");

class HtmlBuilder
{
	private $doc;
	private $rootnode;
	private $article;
	
	public function HtmlBuilder()
	{
		$this->Reset();
	}
	
	public function &Reset()
	{
		PrintHtmlComment("Creating a new DOMDocument!");
		$this->doc = new DOMDocument();
		if($this->doc==NULL)
			Print("doc==NULL:");
		return $this->doc;
	}
	
	public function &GetDoc()
	{
		if(NULL==$this->doc)
		{
			$doc = $this->Reset();
			return $doc;
		}
		else
			return $this->doc;
	}
	public function &GetRoot()
	{
		return $this->rootnode;
	}
	
	public function &AddMenuEntry($menuTuple, $index, $safePic=false)
	{
	    $link = $this->doc->createElement( 'a' );
	    $anchorTarget = "#paragraph_".MakeSafeString(EncodeUmlaute($menuTuple->url));
	    $link->setAttribute("href", $anchorTarget);
	    $ele = $this->doc->createElement( 'div' );
	    $index = sprintf("%02d", $index+1);
//		print("titleArray:".$menuTuple->title."\n");
	    $ele->nodeValue = $menuTuple->title;
		$secNaviRand = rand(0,9);
	    $ele->setAttribute("class", "secNavi");
	    $ele->setAttribute("id", "secNavi".$index);
	    $link->appendChild($ele);
	    return $link;
	}
	
	public function CreateImage($src, $altText=NULL)
	{
	    $pic = $this->GetDoc()->createElement( "img" );
		$pic->setAttribute("src", $src);
		if(NULL!=$altText)
			$pic->setAttribute("alt", $altText);
		return $pic;
	}

	public function AddTag($tag, $id=NULL, $class=NULL, $inhalt=NULL)
	{
		if(NULL==$this->GetDoc())
		{
			Print("no doc!");
			return NULL;
		}
	    $div = $this->GetDoc()->createElement( $tag );
	    if(NULL!=$id)
		    $div->setAttribute("id", $id);
	    if(NULL!=$class)
	    	$div->setAttribute("class", $class);
	    if(NULL!=$inhalt)
		    $div->nodeValue = $inhalt;
	    return $div;
	}
	
	public function AddImage($picID, $align=NULL, $paraImage=false)
	{
		$result = DBCntrl::GetInst()->GetPicData($picID);

		$picDiv = $this->doc->createElement("div");
	    switch($align)
	    {
	    	case PicPara::eTYPE_PIC_LEFT:
			    $picDiv->setAttribute("class", "picFrame picFrameLeft");
	    		break;
	    	default:
	    		$picDiv->setAttribute("class", "picFrame picFrameRight");
	    		break;
	    }

		$picLink = $this->doc->createElement("a");
	    $pic = $this->doc->createElement( "img" );
	    $pic->setAttribute("src", $result["url"]);
		if($paraImage)
		    $pic->setAttribute("class", "paraContentImage");
		$picLink->appendChild($pic);
		$picLink->setAttribute("href", $result["url"]);
		$picLink->setAttribute("target", "_blank");
		$picLink->setAttribute("rel", "lightbox[articlePic]");
	    $picDiv->appendChild($picLink);
	    $title = $this->doc->createElement( "div" );
	    $title->nodeValue = $result["title"];
	    $picDiv->appendChild($title);
	    return $picDiv;
	}	

	public function AddStyle($div, $styleString)
	{
		$div->setAttribute("style", $styleString);
	}

	public function AddText($div, $string)
	{
		$div->nodeValue = htmlspecialchars_decode($string);
	}

	public function Render()
	{
//		PrintHtmlComment("render that bugger! ".$this->doc->saveXML());
	    print $this->doc->saveHTML();
    }
    
  //////////////////////////////////////////
  
    public function BuildFormForLink($value)
    {
    	$returnString = "<form method='post' action='#'>";
    	$returnString .= "<input name='linkMenu' type='hidden' value='$value' />";
    	$returnString .= "<input name='Submit' type='submit' value='$value' class='hiddenFormButton' />";
    	$returnString .= "</form>";
    	return $returnString;
    }
    
      
}

?>