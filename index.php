<?php
// Klassendefinitionen einbinden und Session �bernehmen
//require_once("cgi-bin/Aufenthalt.php");
require_once("cgi-bin/Content/ContentMgr.php");
session_start();
ContentMgr::GetInstance()->Genesis();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<?php 
	ContentMgr::GetInstance()->RenderHeader();
?>
<link href="/css/mainstyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/script/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="/script/mainScript.js"></script>
</head>

<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1&appId=133353956731320";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<!-- Navigation //-->
<!-- Navigation 		
<div id="menuLeft">
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

		<object id="flash1" data="/images/Navi.swf" type="application/x-shockwave-flash" width="140" height="440">
			<param name="movie" value="images/Navi.swf" />
			<param name="wmode" value="transparent" />
		</object>

</div>
//-->

<!-- Header and deco //-->
<div id="header" class="redbordered"><img alt="" src="/images/layout/BG_02.jpg" /></div>
<div id="Deco01"><img alt="" src="/images/layout/Deco.png" /></div>
<!--
<div id="articleTitle">
 	<object id="flash2" data="/images/ArticleTitle.swf" height="200" type="application/x-shockwave-flash" width="200">
		<param name="movie" value="images/ArticleTitle.swf" />
		<param name="flashvars" value="titleString=Gunnar" />
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="wmode" value="transparent" />
	</object>
</div>
//-->

<!-- Content //-->
<?php 
	ContentMgr::GetInstance()->RenderContent();
?>

<div id="advertRight">
	Werbung:
	<hr noshade />
	<SCRIPT charset="utf-8" type="text/javascript" src="http://ws.amazon.de/widgets/q?rt=tf_sw&ServiceVersion=20070822&MarketPlace=DE&ID=V20070822/DE/rockmaga-21/8002/ba8f7bfe-18ec-49cb-a596-3bc5b18a626e"> </SCRIPT> <NOSCRIPT><A HREF="http://ws.amazon.de/widgets/q?rt=tf_sw&ServiceVersion=20070822&MarketPlace=DE&ID=V20070822%2FDE%2Frockmaga-21%2F8002%2Fba8f7bfe-18ec-49cb-a596-3bc5b18a626e&Operation=NoScript">Amazon.de Widgets</A></NOSCRIPT>
	<script type="text/javascript">
		<!--
			google_ad_client = "ca-pub-7125840293910524";
			/* Hauptseite */
			google_ad_slot = "2792988891";
			google_ad_width = 160;
			google_ad_height = 600;
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>

</div>	
</body>
</html>
