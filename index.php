<?php
// Klassendefinitionen einbinden und Session �bernehmen
//require_once("cgi-bin/Aufenthalt.php");
require_once("cgi-bin/Content/ContentMgr.php");
session_start();
Aufenthalt::GetInstance()->DBConn()->schliesse();
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

<!-- Header and deco //-->
<div id="header" class="redbordered"><img alt="" src="/images/layout/BG_02.jpg" /></div>
<div id="Deco01"><img alt="" src="/images/layout/Deco.png" /></div>

<!-- Content //-->
<?php 
	ContentMgr::GetInstance()->RenderContent();
?>

</div>	
</body>
</html>
