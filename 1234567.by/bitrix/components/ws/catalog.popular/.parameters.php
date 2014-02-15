<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arCatalogIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["CATALOG_IBLOCK_TYPE"]!="-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arCatalogIBlocks[$arRes["ID"]] = $arRes["NAME"];

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
        "CATALOG_IBLOCK_TYPE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CATALOG_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ),
        "CATALOG_IBLOCK_ID" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CATALOG_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arCatalogIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),
        "ELEMENT_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_ELEMENT_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
        ),
        "ELEMENT_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_ELEMENT_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["ELEMENT_CODE"]}',
        ),
        "SECTION_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_SECTION_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
        ),
        "SECTION_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("CP_BCSL_SECTION_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["SECTION_CODE"]}',
        ),
        "PRICE_CODE" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arPrice,
        )
    )
);
?>
