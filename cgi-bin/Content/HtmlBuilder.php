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
//		print "Creating a HTML BUILDER!\n";
		$this->doc = new DOMDocument();
		return $this->doc;
	}
	
	public function &GetDoc()
	{
		return $this->doc;
	}
	public function &GetRoot()
	{
		return $this->rootnode;
	}
	
	public function &AddMenuEntry($menuTuple, $index, $safePic=false)
	{
	    $link = $this->doc->createElement( 'a' );
	    $link->setAttribute("href", $menuTuple->url);
	    $ele = $this->doc->createElement( 'div' );
	    $index = sprintf("%02d", $index+1);
//		print("titleArray:".$menuTuple->title."\n");
	    $ele->nodeValue = $menuTuple->title;
	    $ele->setAttribute("class", "secNavi");
	    $ele->setAttribute("id", "secNavi".$index);
	    $link->appendChild($ele);
	    return $link;
	}

	public function AddTag($tag, $id=NULL, $class=NULL, $inhalt=NULL)
	{
	    $div = $this->doc->createElement( $tag );
	    if(NULL!=$id)
		    $div->setAttribute("id", $id);
	    if(NULL!=$class)
	    	$div->setAttribute("class", $class);
	    if(NULL!=$inhalt)
		    $div->nodeValue = $inhalt;
	    return $div;
	}
	
	public function AddImage($picID, $align=NULL)
	{
		$conn = Aufenthalt::GetInstance()->GetConn();
		$result = $conn->GetPicData($picID);

		$picDiv = $this->doc->createElement("div");
	    switch($align)
	    {
	    	case PicPara::$ePIC_LEFT:
			    $picDiv->setAttribute("class", "picFrame picFrameLeft");
	    		break;
	    	default:
	    		$picDiv->setAttribute("class", "picFrame picFrameRight");
	    		break;
	    }

	    $pic = $this->doc->createElement( "img" );
	    $pic->setAttribute("src", $result["url"]);
	    $picDiv->appendChild($pic);
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
	    print $this->doc->saveHTML();
    }
}

?>