<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
    return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

	
$arShowFields = array(
    "NAME" => GetMessage("ELEMENT_NAME"),
    "PREVIEW_PICTURE" => GetMessage("ELEMENT_PREVIEW_PICTURE"),
    "DETAIL_PAGE_URL" => GetMessage("ELEMENT_DETAIL_PAGE_URL"),
);

$arComponentParameters = Array(
	"GROUPS" => array(
		"LINK_IBLOCK_PARAMS" => array(
			"NAME" => GetMessage("LINK_IBLOCK_PARAMS"),
		),
		"IMAGES" => array(
		    "NAME"	=> GetMessage("IMAGES")
		),
	),
	"PARAMETERS" => Array(
		"PATH_TO_ORDER" => Array(
			"NAME" => GetMessage("PATH_TO_ORDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/order/make/",
			"PARENT" => "BASE",
		),
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/basket.php",
			"PARENT" => "BASE",
		),
		
		"IBLOCK_TYPE" => array(
			"PARENT" => "LINK_IBLOCK_PARAMS",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "LINK_IBLOCK_PARAMS",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SHOW_FIELDS"=>array(
			"PARENT" => "LINK_IBLOCK_PARAMS",
			"NAME" => GetMessage("SHOW_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arShowFields,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"LINK_FIELD"=>array(
			"PARENT" => "LINK_IBLOCK_PARAMS",
			"NAME" => GetMessage("LINK_FIELD"),
			"TYPE" => "STRING",
		),
		"BLOCK_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BLOCK_NAME"),
			"TYPE" => "STRING",
		),
		"IMAGES_MAX_HEIGHT" => array(
			"PARENT" => "IMAGES",
			"NAME" => GetMessage("IMAGES_MAX_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"IMAGES_MAX_WIDTH" => array(
			"PARENT" => "IMAGES",
			"NAME" => GetMessage("IMAGES_MAX_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "41",
		),		
 	)
);
?>