<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
    Processing of received parameters
 *************************************************************************/

// Основные настройки
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["SECTION_CODE"] = trim($_REQUEST["SECTION_CODE"]);
$arParams["SECTION_ID"] = (int) $_REQUEST["SECTION_ID"];
$arParams["ELEMENT_CODE"] = trim($_REQUEST["ELEMENT_CODE"]);
$arParams["ELEMENT_ID"] = (int) $_REQUEST["ELEMENT_ID"];

$arParams["ITEMS_COUNT"] = intval($arParams["ITEMS_COUNT"]);
if($arParams["ITEMS_COUNT"]<=0)
    $arParams["ITEMS_COUNT"] = 20;

$arParams["CACHE_TIME"] = "3600";
$arParams["CACHE_TYPE"] = "A";

/**
 * Параметры масштабирования изображений
 */
$arParams["IMAGES_MAX_HEIGHT"] = $arParams["IMAGES_MAX_HEIGHT"];
$arParams["IMAGES_MAX_HEIGHT"]  = $arParams["IMAGES_MAX_HEIGHT"] > 0 ? $arParams["IMAGES_MAX_HEIGHT"] : "83";
$arParams["IMAGES_MAX_WIDTH"] = $arParams["IMAGES_MAX_WIDTH"];
$arParams["IMAGES_MAX_WIDTH"]  = $arParams["IMAGES_MAX_WIDTH"] > 0 ? $arParams["IMAGES_MAX_WIDTH"] : "123";
$arParams["NO_PHOTO_SRC"] = "/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png";

if(!is_array($arParams["PRICE_CODE"]))
    $arParams["PRICE_CODE"] = array();

// Валюта
$arParams["SEC_CURRENCY"] = "USD";
// Основная валюта
$arParams["MAIN_CURRENCY"] = "BYR";

/*************************************************************************
    Work with cache
 *************************************************************************/
$obCache = new CPHPCache;
$cache_id = $this->GetCacheID(serialize($arrFilter).$arNavigation["SIZEN"].$arNavigation["PAGEN"]);

// work
if($obCache->InitCache($arParams["CACHE_TIME"], $cache_id, "/")) {
    $arResult  =  $obCache->GetVars();
} else {
    $obCache->StartDataCache();

    if(!CModule::IncludeModule("iblock")) {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }
    /**
     *  Сохранение id кеша элемента
     */
    $curUrl = $APPLICATION->GetCurPageParam("", array("logout", "login", "clear_cache"));

    if($arParams["CACHE_TYPE"] != "N")
    {
        if(CModule::IncludeModule("nscache")) {
            if(strlen($arParams["SECTION_CODE"]) > 0)
                CExCacheM::SaveSectionCacheID($arParams["SECTION_CODE"], $cache_id, $curUrl);
            else
                CExCacheM::SaveCacheID("/", $cache_id);
        }
    }

    if($arParams["CATALOG_IBLOCK_ID"] > 0) {
        // инфо о разделе
        if(strlen($arParams["SECTION_CODE"]) > 0 || $arParams["SECTION_ID"]) {
            $arFilter = array(
                "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                "ACTIVE" => "Y"
            );
            if($arParams["SECTION_ID"] > 0) {
                $arFilter["ID"] = $arParams["SECTION_ID"];
            } else
                $arFilter["CODE"] = $arParams["SECTION_CODE"];

            $arSelect = array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "DESCRIPTION",
                "LEFT_MARGIN",
                "RIGHT_MARGIN",
                "DEPTH_LEVEL",
                "CODE",
                "LIST_PAGE_URL",
                "SECTION_PAGE_URL",
                "PICTURE",
                "UF_*"
            );

            $arResult["SECTION_IDS"] = array();
            $rsSection = CIBlockSection::GetList(false, $arFilter, false, $arSelect);
            if($arSection = $rsSection->GetNext()) {
                $arResult["SECTION"] = $arSection;
            }
        }

        //EXECUTE
        if($arParams["CATALOG_IBLOCK_ID"] > 0) {

            $arResult["ITEMS"] = array();
            $arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices("", $arParams["PRICE_CODE"]);

            $arFilter = array(
                "ACTIVE"=>"Y",
                "IBLOCK_ID"=>$arParams["CATALOG_IBLOCK_ID"],
                ">CATALOG_QUANTITY" => 0
            );
            if(is_array($arResult["SECTION"])) {
                $arFilter["SECTION_ID"] = $arResult["SECTION"]["ID"];
                $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
            }

            $arSelect = array(
                "ID",
                "NAME",
                "CODE",
                "IBLOCK_ID",
                "DETAIL_PAGE_URL",
                "DETAIL_PICTURE"
            );

            foreach($arResult["PRICES"] as $key => $value) {
                /*if($arParams["ELEMENT_SORT_FIELD"] == "price") {
                    $arSort = array(
                        "CATALOG_PRICE_".$value["ID"] => $arParams["ELEMENT_SORT_ORDER"]
                    );
                }*/
                $arSelect[] = $value["SELECT"];
            }
			          $arSort = array(
                "SHOW_COUNTER" => "DESC"
            );

			//сортировка по свойству "COUNTER_OF_VIEWINGS"
			$arSort = array(
                "PROPERTY_COUNTER_OF_VIEWINGS" => "DESC");

            $rsElements = CIBlockElement::GetList($arSort, $arFilter, false, array("nPageSize" => 20), $arSelect);
            $arResult["ITEMS_COUNT"] = $rsElements->SelectedRowsCount();

            while($rsItem = $rsElements->GetNextElement()) {
                $arItem = $rsItem->GetFields();
                $arItem["P"] = $rsItem->GetProperties();
				//printAdmin($arItem);
                // изображения
                $arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
                //printObj($arItem);

                $mode = "trim";
                //проверка на отсуствие фото..фраза для проверки вхождения в DESCRIPTION прописана в init.php
                if(is_array($arItem["DETAIL_PICTURE"]))
                    $pictureSrc = $arItem["DETAIL_PICTURE"]["SRC"];
                else {
                    $pictureSrc = $arParams["NO_PHOTO_SRC"];
                    $mode = "in";
                }

                $maxHeight = $arParams["IMAGES_MAX_HEIGHT"];
                $maxWidth = $arParams["IMAGES_MAX_WIDTH"];
                //IMAGES_LIST_MAX_HEIGHT
                $arImageParams = array(
                    "WIDTH" => $arParams["IMAGES_MAX_HEIGHT"],
                    "HEIGHT"    => $arParams["IMAGES_MAX_HEIGHT"],
                    "SRC"   => CWsImageTools::Resizer("/thumb/".$maxWidth."x".$maxHeight."x".$mode.$pictureSrc)
                );
                $arItem["PREVIEW_PICTURE"] = $arImageParams;
                unset($arItem["DETAIL_PICTURE"]);

                //  цены
                $arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arItem["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams['PRICE_VAT_INCLUDE']);

                if(is_array($arItem["PRICES"]))
                    $arItem["PRICES"] = CWsCatalogTools::ConvertPrice($arItem["PRICES"], $arParams);

                $arResult["ITEMS"][]=$arItem;
            }




            $arResult["TOTAL_ITEMS"] = count($arResult["ITEMS"]);
        }
    }
    $obCache->EndDataCache($arResult);
}

$this->IncludeComponentTemplate();