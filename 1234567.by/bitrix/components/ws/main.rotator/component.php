<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
    return GetMessage("IBLOCK_MODULE_NOT_INSTALLED");

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

$arParams["ELEMENT_COUNT"] = intval($arParams["ELEMENT_COUNT"]);
if($arParams["ELEMENT_COUNT"] == 0)
	$arParams["ELEMENT_COUNT"] = 5;

$obCache = new CPHPCache;
$cache_id = $this->GetCacheID();

if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, "/"))
    $arResult  =  $obCache->GetVars();
else
{
    if($obCache->StartDataCache())
    {
    	if(strlen($arParams["IBLOCK_TYPE"]) == 0) {
    		ShowError(GetMessage("IBLOCK_TYPE_NOT_SET"));
    		return;
    	}
    		
    	// получаем список элементов, у которых установлена привязка к товару и активен
    	$arFilter = array(
    		//"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
    		"ACTIVE" => "Y",
    		"ACTIVE_DATE" => "Y",
            array("LOGIC"=>"OR", 
                array(">PREVIEW_PICTURE" =>0), 
                array("!PROPERTY_LINK" => array("", false)))
        );
           
        $arIds = array();
        $arRotatorEls = array();
        $arSelect = array(
            "ID",
            "NAME",
            "PREVIEW_PICTURE",
            "DETAIL_TEXT",
            "PREVIEW_TEXT",
            "SORT",
            "PROPERTY_PRODUCT",
            "PROPERTY_LINK"
        );
        
        $rsElements = CIBlockElement::GetList(array("SORT"=>"ASC", "ID"=>"DESC"), $arFilter, false, array("nTopCount" => "100"), $arSelect);
        while($arElement = $rsElements->GetNext())
        {
            if(intval($arElement["PROPERTY_PRODUCT_VALUE"]) > 0)
            {
                $arIds[] = $arElement["PROPERTY_PRODUCT_VALUE"];
                $arRotatorEls[$arElement["PROPERTY_PRODUCT_VALUE"]] = $arElement;
                
                $arResult["ITEMS"][$arElement["ID"]] = $arElement;
            }
            elseif(strlen($arElement["PROPERTY_LINK_VALUE"]) > 0 && intval($arElement["PREVIEW_PICTURE"]) > 0)
            {
                $arElement["PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
                
                if(is_array($arElement["PICTURE"]))
                {
                    $needResize = false;
                    /*if($arElement["PICTURE"]["WIDTH"] > $arParams["IMAGES_BANNER_MAX_WIDTH"])
                    {
                        $w = $arParams["IMAGES_BANNER_MAX_WIDTH"];
                        $h = $arParams["IMAGES_BANNER_MAX_HEIGHT"];
                    }
                    elseif($arElement["PICTURE"]["WIDTH"] < $arParams["IMAGES_BANNER_MAX_WIDTH"])
                    {
                        $w = $arParams["IMAGES_MAX_WIDTH"];
                        $h = $arParams["IMAGES_MAX_HEIGHT"];
                    }
                    else
                        $needResize = false;
                    */
                    if($needResize)
                    {
                        $arElement["PICTURE"] = array(
                            "SRC" =>    resizer("/thumb/".$w."x".$h."xin".$arElement["PICTURE"]["SRC"]),
                            "WIDTH" => $w,
                            "HEIGHT" => $h
                        );
                    }
                    else
                    {
                        $arElement["PICTURE"] = array(
                            "SRC" =>    $arElement["PICTURE"]["SRC"],
                            "WIDTH" => $arElement["PICTURE"]["WIDTH"],
                            "HEIGHT" => $arElement["PICTURE"]["HEIGHT"]
                        );
                    }
                    
                    $arElement["DETAIL_PAGE_URL"] = trim($arElement["PROPERTY_LINK_VALUE"]);
                    $arResult["ITEMS"][$arElement["ID"]] = $arElement;
                }
            }
        }
       $obCache->EndDataCache($arResult);
	}
}
$this->IncludeComponentTemplate();