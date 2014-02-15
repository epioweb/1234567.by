<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
    "GROUPS" => array(
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
