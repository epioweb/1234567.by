<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsBannersIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["BANNERS_IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsBannersIBlock->Fetch())
	$arBannersIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["STRUCTURE_IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arStructureIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_UF = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["STRUCTURE_IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField)
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;

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

$arComponentParameters = array(
	"GROUPS" => array(
	   "PRICES" => array(
            "NAME" => GetMessage("IBLOCK_PRICES"),
        ),
    
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "N",
		),
		"BANNERS_IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_BANNERS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"BANNERS_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_BANNERS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arBannersIBlock,
			"REFRESH" => "N",
		),
		"STRUCTURE_IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_STRUCTURE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"STRUCTURE_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_STRUCTURE_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arStructureIBlock,
			"REFRESH" => "Y",
		),
		"UF_LINK_PROP_CODE" =>array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CP_BCSL_SECTION_UF_LINK_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty_UF,
        ),
        "ELEMENT_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ELEMENT_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "5",
        ),
        "DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("IBLOCK_DETAIL_URL"),
			"",
			"URL_TEMPLATES"
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("IBLOCK_SECTION_URL"),
			"",
			"URL_TEMPLATES"
		),
        "BASKET_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("IBLOCK_BASKET_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "/personal/basket.php",
        ),
        "ACTION_VARIABLE" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("IBLOCK_ACTION_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "action",
        ),
        "PRODUCT_ID_VARIABLE" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "id",
        ),
        "PRODUCT_QUANTITY_VARIABLE" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("CP_BCE_PRODUCT_QUANTITY_VARIABLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "quantity",
        ),
        "PRICE_CODE" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arPrice,
        ),
        "USE_PRICE_COUNT" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            ),
        "SHOW_PRICE_COUNT" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "1",
        ),
        "PRICE_VAT_INCLUDE" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "PRICE_VAT_SHOW_VALUE" => array(
            "PARENT" => "PRICES",
            "NAME" => GetMessage("IBLOCK_VAT_SHOW_VALUE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
?>
