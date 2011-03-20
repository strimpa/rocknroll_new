<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="mainstyles.css" rel="stylesheet" type="text/css" />
</head>

<body>

<!-- Navigation //-->
<div id="menuLeft">
<!-- Navigation 		
	<div id="menu">
		<img height="52" src="images/layout/Menu_04.png" width="134" /><br />
		<img height="49" src="images/layout/Menu_06.png" width="134" /><br />
		<img height="49" src="images/layout/Menu_07.png" width="134" /><br />
		<img height="52" src="images/layout/Menu_08.png" width="134" /><br />
		<img height="49" src="images/layout/Menu_11.png" width="134" /><br />
		<img height="49" src="images/layout/Menu_12.png" width="134" /><br />
		<img height="52" src="images/layout/Menu_13.png" width="134" /><br />
		<img height="49" src="images/layout/Menu_14.png" width="134" /><br />
		<img height="50" src="images/layout/Menu_15.png" width="134" />
	</div>
//-->

		<object id="flash1" data="images/Navi.swf" type="application/x-shockwave-flash" width="140" height="440">
			<param name="movie" value="images/Navi.swf" />
			<param name="wmode" value="transparent" />
		</object>

</div>

<!-- Header and deco //-->
<div id="header" class="redbordered"><img alt="" src="images/layout/BG_02.jpg" /></div>
<div id="contentLeftOverlap"><img alt="" src="images/layout/BG_04.jpg" /></div>
<div id="Deco01"><img alt="" src="images/layout/Deco_08.png" /></div>
<div id="articleTitle">
	
	<object id="flash2" data="images/ArticleTitle.swf" height="200" type="application/x-shockwave-flash" width="200">
		<param name="movie" value="images/ArticleTitle.swf" />
		<param name="flashvars" value="titleString=Gunnar" />
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="wmode" value="transparent" />
	</object>
	
</div>

<!-- Content //-->
<?php 
	require_once("cgi-bin/Content/ContentMgr.php");
	ContentMgr::GetInstance()->RenderContent();
?>

</body>
</html>
