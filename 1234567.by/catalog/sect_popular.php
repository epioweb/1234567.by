<?$APPLICATION->IncludeComponent("ws:catalog.popular", ".default", array(
	"IBLOCK_TYPE" => "info",
	"IBLOCK_ID" => "7",
	"CATALOG_IBLOCK_TYPE" => "1c_catalog",
	"CATALOG_IBLOCK_ID" => "6",
	"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
	"ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
	"PRICE_CODE" => array(
		0 => "Розничная",
	)
	),
	false
);?>