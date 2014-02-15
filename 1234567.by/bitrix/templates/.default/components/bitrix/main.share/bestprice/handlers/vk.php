<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/vk.php");
global $APPLICATION;
$url = $APPLICATION->GetCurPage();
$name = "vk";
$title = GetMessage("BOOKMARK_HANDLER_VK");

$icon_url_template = '<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="//vk.com/js/api/openapi.js?96"></script>

<script type="text/javascript">
  VK.init({apiId: 3693575, onlyWidgets: true});
</script>

<!-- Put this div tag to the place, where the Like block will be -->
<div id="vk_like"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "button"});
</script>

';

/*$icon_url_template = "<script>\n".
	"if (__function_exists('vk_click') == false) \n".
	"{\n".
		"function vk_click(url) \n".
		"{ \n".
			"window.open('http://vkontakte.ru/share.php?url='+encodeURIComponent(url),'sharer','toolbar=0,status=0,width=626,height=436'); \n".
			"return false; \n".
		"} \n".
	"}\n".
	"</script>\n".
	"<a href=\"http://vkontakte.ru/share.php?url=#PAGE_URL#\" onclick=\"return vk_click('#PAGE_URL#');\" target=\"_blank\" class=\"vk\" title=\"".$title."\"></a>\n";*/
$sort = 400;
?>