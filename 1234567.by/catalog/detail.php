<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>

<?$APPLICATION->IncludeComponent("ws:catalog.detail", ".default", array(
	"IBLOCK_TYPE" => "1c_catalog",
	"IBLOCK_ID" => "6",
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
	"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
	"ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
	"SET_TITLE" => "Y",
	"PRICE_CODE" => array(
		0 => "Розничная",
	),
	"SEO_TITLE_PATTERN" => "Купить #PRODUCT_NAME# в Минске",
	"SEO_DESCRIPTION_PATTERN" => "Купить #PRODUCT_NAME# в Минске - описание",
	"SEO_KEYWORDS_PATTERN" => "#PRODUCT_NAME#",
	"BROWSER_TITLE" => "BROWSER_TITLE",
	"BROWSER_TITLE_TEMPLATE" => "",
	"SEO_NAME" => "SEO_NAME1",
	"META_DESCRIPTION" => "META_DESCRIPTION",
	"META_KEYWORDS" => "META_KEYWORDS"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>