<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/facebook.php");
$name = "facebook";
$title = GetMessage("BOOKMARK_HANDLER_FACEBOOK");
$icon_url_template = '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script><div class="fb-like" data-href="http://'.$_SERVER["SERVER_NAME"].$APPLICATION->GetCurPage().'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true" data-action="recommend"></div>';
$sort = 100;
?>