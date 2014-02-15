<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];


$arPrice = array();
if(CModule::IncludeModule("catalog"))
{
    $rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
    while($arr=$rsPrice->Fetch())
    {
        $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
        $arPriceForSortField[$arr["ID"]] =  "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
    }
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
        "PRICES" => array(
            "NAME" => GetMessage("IBLOCK_PRICES"),
        ),
        "SEO" => array(
            "NAME" => GetMessage("SEO_BLOCK")
        )
    ),
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),
        "SECTION_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_SECTION_ID"),
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
            "DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
        ),
        "SECTION_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_SECTION_CODE"),
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
            "DEFAULT" => '={$_REQUEST["SECTION_CODE"]}',
        ),
        "BROWSER_TITLE" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("BROWSER_TITLE"),
            "TYPE" => "STRING",
            "DEFAULT" => 'UF_BROWSER_TITLE',
        ),
        "BROWSER_TITLE_TEMPLATE" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("BROWSER_TITLE_TEMPLATE"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
        ),
        "SEO_NAME" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("SEO_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => 'UF_SEO_NAME',
        ),
        "META_DESCRIPTION" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("META_DESCRIPTION"),
            "TYPE" => "STRING",
            "DEFAULT" => 'UF_META_DESCRIPTION',
        ),
        "META_KEYWORDS" => array(
            "PARENT" => "BX_IBLOCK_SETTINGS",
            "NAME" => GetMessage("META_KEYWORDS"),
            "TYPE" => "STRING",
            "DEFAULT" => 'UF_META_KEYWORDS',
        ),
        "PRICE_CODE" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arPrice,
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
        "SET_TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SET_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "SEO_TITLE_PATTERN" => array(
            "PARENT" => "SEO",
            "NAME" => GetMessage("SEO_TITLE_PATTERN"),
            "TYPE" => "STRING",
            "HINT" => GetMessage("SEO_HINT")
        ),
        "SEO_DESCRIPTION_PATTERN" => array(
            "PARENT" => "SEO",
            "NAME" => GetMessage("SEO_DESCRIPTION_PATTERN"),
            "TYPE" => "STRING",
            "HINT" => GetMessage("SEO_HINT")
        ),
        "SEO_KEYWORDS_PATTERN" => array(
            "PARENT" => "SEO",
            "NAME" => GetMessage("SEO_KEYWORDS_PATTERN"),
            "TYPE" => "STRING",
            "HINT" => GetMessage("SEO_HINT")
        )
    )
);
?>
