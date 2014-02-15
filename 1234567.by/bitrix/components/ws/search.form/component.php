<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!IsModuleInstalled("search"))
{
	ShowError(GetMessage("BSF_C_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("iblock"))
{
	//ShowError(GetMessage("IBLOCK_MODULE_UNAVAILABLE"));
	return;
}

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600;

$arParams["WHERE"] = intval($_REQUEST["where"]);
$arParams["Q"] = trim($_REQUEST["q"]);

//variables from component
if(!isset($arParams["PAGE"]) || strlen($arParams["PAGE"])<=0)
	$arParams["PAGE"] = "#SITE_DIR#search/index.php";

$arResult["FORM_ACTION"] = htmlspecialchars(str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]));
$arParams["DFLT_STRING"] = "Что ищем?";

if($this->StartResultCache($arParams["CACHE_TIME"], ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
    // получим список инфблоков, которые уже привязаны
    $arFilter = array(
        "IBLOCK_ID" => $arParams["STRUCTURE_IBLOCK_ID"],
        "ACTIVE" => "Y",
        ">UF_LINK_IBLOCK_ID" => 0
    );
    $arSelect = array(
        "IBLOCK_ID",
        "UF_LINK_IBLOCK_ID"
    );
    
    $arIblockIds = array();
    $rsSections = CIBlockSection::GetList(array("ID"=>"ASC"), $arFilter, false, $arSelect);
    while($arSection = $rsSections->GetNext())
    {
        if(!in_array($arSection["UF_LINK_IBLOCK_ID"], $arIblockIds))
            $arIblockIds[] = $arSection["UF_LINK_IBLOCK_ID"];
    }
    
    $res = CIBlock::GetList(Array("NAME"=>"ASC"),Array('TYPE'=>'1c_catalog'),false);
    while($ar_res = $res->Fetch())
    {
        if(in_array($ar_res["IBLOCK_ID"], $arIblockIds))
        {
            $bOk = true;
            // если обычный смертный, прячем иблоки,в которых нет в наличии товара
            if(SHOW_PRODUCTS_IN_STOCK)
            {
                $tmpFilter = array(
                    "IBLOCK_ID" => $ar_res["IBLOCK_ID"],
                    ">CATALOG_QUANTITY" => 0,
                    "ACTIVE" =>"Y"
                );
                $rsEl = CIBlockElement::GetList(array("ID"=>"ASC"), $tmpFilter, array(), false);
                if($rsEl == 0)
                    $bOk = false;
            }
            
            if($bOk)
            {
                $ar_res = ClearArr($ar_res, array("ID", "NAME"));
                $arResult['CATEGORIES'][] =  $ar_res;
            }
        }   
    }
    
    $this->SetResultCacheKeys(array(
        "CATEGORIES",
    ));
    $this->IncludeComponentTemplate();
}
?>
