<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/vk.php");
global $APPLICATION;
$url = $APPLICATION->GetCurPage();
$name = "odnoklassniki";
$title = GetMessage("BOOKMARK_HANDLER_ODN");

$icon_url_template = '<div id="ok_shareWidget"></div>
<script>
!function (d, id, did, st) {
  var js = d.createElement("script");
  js.src = "http://connect.ok.ru/connect.js";
  js.onload = js.onreadystatechange = function () {
  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
    if (!this.executed) {
      this.executed = true;
      setTimeout(function () {
        OK.CONNECT.insertShareWidget(id,did,st);
      }, 0);
    }
  }};
  d.documentElement.appendChild(js);
}(document,"ok_shareWidget","http://' .$_SERVER["SERVER_NAME"]. '/","{width:100,height:30,st:\'oval\',sz:20,nt:1}");
</script>';
$sort = 200;