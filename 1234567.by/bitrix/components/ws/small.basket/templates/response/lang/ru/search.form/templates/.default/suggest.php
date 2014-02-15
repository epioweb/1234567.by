<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$_REQUEST["q"] = utf8win1251($_REQUEST["q"]);?>
<?$APPLICATION->IncludeComponent("newsite:search.page", "response", array(
	"PRICE_CODE" => array(
		0 => "Оптовая2 BYR",
	),
	"DEFAULT_SORT" => "rank",
	"PAGE_ELEMENT_COUNT" => "7",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"PAGER_TITLE" => "Результаты поиска",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "main",
	"1C_IBLOCK_TYPE" => "1c_catalog",
	"1C_IBLOCK_ID" => "10",
	"1C_LINK_FIELD" => "PROPERTY_BX_CML2_LINK",
	"IMAGES_MAX_HEIGHT" => "40",
	"IMAGES_MAX_WIDTH" => "40",
	"USE_SUGGEST" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>