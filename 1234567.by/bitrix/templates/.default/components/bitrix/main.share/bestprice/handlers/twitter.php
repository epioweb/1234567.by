<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/twitter.php");
$name = "twitter";
$title = GetMessage("BOOKMARK_HANDLER_TWITTER");

if (
	is_array($arParams)
	&& array_key_exists("SHORTEN_URL_LOGIN", $arParams) 
	&& strlen(trim($arParams["SHORTEN_URL_LOGIN"])) > 0
	&& array_key_exists("SHORTEN_URL_KEY", $arParams) 
	&& strlen(trim($arParams["SHORTEN_URL_KEY"])) > 0
)
{
	$icon_url_template = '<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>';
}
else
{
	$icon_url_template = '<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>';
}

$sort = 300;
$charsBack = true;
?>