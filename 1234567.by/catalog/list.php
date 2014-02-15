<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent("ws:catalog.section", ".default", array(
	"IBLOCK_TYPE" => "1c_catalog",
	"IBLOCK_ID" => "6",
	"SECTION_ID" => array(
		0 => $_REQUEST["SECTION_ID"],
		1 => "",
	),
	"SECTION_CODE" => array(
		0 => $_REQUEST["SECTION_CODE"],
		1 => "",
	),
	"SET_TITLE" => "Y",
	"PRICE_CODE" => array(
		0 => "Розничная",
	),
	"SEO_TITLE_PATTERN" => "Купить #NAME# в Минске",
	"SEO_DESCRIPTION_PATTERN" => "Купить #NAME# в Минске",
	"SEO_KEYWORDS_PATTERN" => "#NAME#",
	"BROWSER_TITLE" => "UF_BROWSER_TITLE",
	"BROWSER_TITLE_TEMPLATE" => "",
	"SEO_NAME" => "UF_SEO_NAME",
	"META_DESCRIPTION" => "UF_META_DESCRIPTION",
	"META_KEYWORDS" => "UF_META_KEYWORDS",
	"ELEMENT_SORT_FIELD" => "name",
	"ELEMENT_SORT_ORDER" => "",
	"ELEMENT_SORT_ARRAY_FIELDS" => array(
		0 => "shows",
		1 => "name",
		2 => "price",
		3 => "",
	)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>