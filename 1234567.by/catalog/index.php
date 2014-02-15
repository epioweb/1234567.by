<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent("ws:catalog.section.list", ".default", array(
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
	"SEO_TITLE_PATTERN" => "Купить #NAME# в Минске",
	"SEO_DESCRIPTION_PATTERN" => "Купить #NAME# в Минске",
	"SEO_KEYWORDS_PATTERN" => "#NAME#"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>