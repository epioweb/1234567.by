<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(" 1234567.by - магазин Бэстпрайс. Аксессуары для мобильных телефонов, сумки, чехлы, аккумуляторы, зарядные устройства, кабеля, карты памяти, flash");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
?> 




<?$APPLICATION->IncludeComponent("ws:main.rotator", ".default", array(
        "IBLOCK_TYPE" => "rotators",
        "IBLOCK_ID" => "8",
        "" => "2433",
        "ELEMENT_COUNT" => "5",
        "DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#.html",
        "SECTION_URL" => "/catalog/#SECTION_CODE#/",
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "600",
        "CACHE_GROUPS" => "Y",
    ),
    false,
    array(
        "ACTIVE_COMPONENT" => "Y"
    )
);?> <?$APPLICATION->IncludeComponent(
	"ws:catalog.section.list",
	"",
	Array(
		"IBLOCK_TYPE" => "1c_catalog",
		"IBLOCK_ID" => "6",
		"SECTION_ID" => array(),
		"SECTION_CODE" => array(),
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N"
	)
);?> 


<?$APPLICATION->IncludeComponent(
	"ws:catalog.popular",
	".default",
	Array(
		"CATALOG_IBLOCK_TYPE" => "1c_catalog",
		"CATALOG_IBLOCK_ID" => "6",
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"PRICE_CODE" => array(0=>"Розничная",)
	)
);?>




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>