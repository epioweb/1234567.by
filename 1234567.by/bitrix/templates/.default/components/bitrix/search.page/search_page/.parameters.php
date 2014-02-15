<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rs1CIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["1C_IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rs1CIBlock->Fetch())
	$ar1CIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];		
		
$arPrice = array();
if(CModule::IncludeModule("catalog"))
{
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}

$arSortValues =  array(
    "shows" => GetMessage("IBLOCK_SORT_SHOWS"),
    "sort" => GetMessage("IBLOCK_SORT_SORT"),
    "timestamp_x" => GetMessage("IBLOCK_SORT_TIMESTAMP"),
    "name" => GetMessage("IBLOCK_SORT_NAME"),
    "id" => GetMessage("IBLOCK_SORT_ID"),
    "active_from" => GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
    "active_to" => GetMessage("IBLOCK_SORT_ACTIVE_TO"),
    "created_date" => GetMessage("IBLOCK_SORT_DATE_CREATE"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PAGER_SETTINGS" => array(
			"NAME" => GetMessage("SEARCH_PAGER_SETTINGS"),
		),
		"1C_IBLOCK_SETTINGS" => array(
			"NAME" => GetMessage("1C_IBLOCK_SETTINGS"),
		),
		"IMAGES" => array(
			"NAME" => GetMessage("IMAGES"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"1C_IBLOCK_TYPE" => array(
			"PARENT" => "1C_IBLOCK_SETTINGS",
			"NAME" => GetMessage("1C_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"1C_IBLOCK_ID" => array(
			"PARENT" => "1C_IBLOCK_SETTINGS",
			"NAME" => GetMessage("1C_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $ar1CIBlock,
			"REFRESH" => "Y",
		),
		"1C_LINK_FIELD" => array(
			"PARENT" => "1C_IBLOCK_SETTINGS",
			"NAME" => GetMessage("1C_LINK_FIELD"),
			"TYPE" => "STRING",
			"DEFAULT" => 'PROPERTY_XML_ID',
		),
		"PRICE_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"DEFAULT_SORT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_SP_DEFAULT_SORT"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "rank",
			"VALUES" => array(
				"rank" => GetMessage("CP_SP_DEFAULT_SORT_RANK"),
				"date" => GetMessage("CP_SP_DEFAULT_SORT_DATE"),
			),
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SEARCH_PAGE_RESULT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "50",
		),
		//BASKET_URL
		"BASKET_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
		),
		//DISPLAY_BOTTOM_PAGER
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"PAGER_TITLE" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("SEARCH_PAGER_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SEARCH_RESULTS"),
		),
		"DISPLAY_BOTTOM_PAGER" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("DISPLAY_BOTTOM_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PAGER_SHOW_ALWAYS" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("SEARCH_PAGER_SHOW_ALWAYS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PAGER_TEMPLATE" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("SEARCH_PAGER_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"IMAGES_MAX_HEIGHT" => array(
			"PARENT" => "IMAGES",
			"NAME" => GetMessage("IMAGES_MAX_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "100",
		),
		"IMAGES_MAX_WIDTH" => array(
			"PARENT" => "IMAGES",
			"NAME" => GetMessage("IMAGES_MAX_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "100",
		),
		"SEARCH_IN_SECTIONS" => array(
		    "PARENT" => "BASE",
            "NAME" => GetMessage("SEARCH_IN_SECTIONS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
		),
        "ELEMENT_SORT_FIELD" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_DEFAULT_SORT_FIELD"),
            "TYPE" => "LIST",
            "VALUES" => $arSortValues,
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "name",
        ),
        "ELEMENT_SORT_ORDER" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
            "TYPE" => "LIST",
            "VALUES" => $arAscDesc,
            "DEFAULT" => "asc",
        ),
        "ELEMENT_SORT_ARRAY_FIELDS" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("IBLOCK_ELEMENT_ARRAY_SORT_FIELD"),
            "TYPE" => "LIST",
            "VALUES" => $arSortValues,
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "sort",
        ),
	),
);

?>