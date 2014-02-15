<?
define("BX_DONT_SKIP_PULL_INIT", true);
require($_SERVER["DOCUMENT_ROOT"]."/desktop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<div id="placeholder-messenger" class="placeholder-messenger"></div>
<?$APPLICATION->IncludeComponent("bitrix:im.messenger", "", Array("DESKTOP" => "Y"), false, Array("HIDE_ICONS" => "Y"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>