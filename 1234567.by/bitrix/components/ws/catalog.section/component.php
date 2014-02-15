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

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if($arParams["PAGE_ELEMENT_COUNT"]<=0)
    $arParams["PAGE_ELEMENT_COUNT"] = 60;

$arParams["MAX_DEPTH"] = 4;
$arParams["MAX_BLOCK_COUNT"] = 3;
$arParams["MAX_VISIBLE_SUB_SECTIONS"] = 8;

$arParams["CACHE_TYPE"] = "Y";

/*
 * Параметры масштабирования изображений
 */
$arParams["IMAGES_MAX_HEIGHT"] = $arParams["IMAGES_MAX_HEIGHT"];
$arParams["IMAGES_MAX_HEIGHT"]  = $arParams["IMAGES_MAX_HEIGHT"] > 0 ? $arParams["IMAGES_MAX_HEIGHT"] : "75";
$arParams["IMAGES_MAX_WIDTH"] = $arParams["IMAGES_MAX_WIDTH"];
$arParams["IMAGES_MAX_WIDTH"]  = $arParams["IMAGES_MAX_WIDTH"] > 0 ? $arParams["IMAGES_MAX_WIDTH"] : "95";
$arParams["NO_PHOTO_SRC"] = "/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png";

CPageOption::SetOptionString("main", "nav_page_in_session", "Y");
// Сортировка
if(strlen($_REQUEST["s"]) > 0)
    $arParams["ELEMENT_SORT_FIELD"] = $_REQUEST["s"];
if(strlen($_REQUEST["o"]) > 0)
    $arParams["ELEMENT_SORT_ORDER"] = $_REQUEST["o"];

if(!in_array($arParams["ELEMENT_SORT_FIELD"], $arParams["ELEMENT_SORT_ARRAY_FIELDS"]))
    $arParams["ELEMENT_SORT_FIELD"] = "name";
$arParams["ELEMENT_SORT_ORDER"] = $arParams["ELEMENT_SORT_ORDER"] == "desc" ? "desc" : "asc";


// обработка постраничной навигации
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

// выводить по
$arParams["SHOW_NUMBERS"] = array(
    20, 40, 60, 80, 100
);
if(intval($_REQUEST["show"]) > 0) {
    if(in_array($_REQUEST["show"], $arParams["SHOW_NUMBERS"]))
        $arParams["PAGE_ELEMENT_COUNT"] = (int) $_REQUEST["show"];
}

$arNavParams = array(
    "nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
    "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
    "bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);

if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
    $arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];


// SEO
// заголовок браузера
$arParams["BROWSER_TITLE"] = trim($arParams["BROWSER_TITLE"]);
// шаблон заголовка браузера
$arParams["BROWSER_TITLE_TEMPLATE"] = trim($arParams["BROWSER_TITLE_TEMPLATE"]);
// SEO название раздела
$arParams["SEO_NAME"] = trim($arParams["SEO_NAME"]);
// Мета описание
$arParams["META_DESCRIPTION"] = trim($arParams["META_DESCRIPTION"]);
// Мета ключевые слова
$arParams["META_KEYWORDS"] = trim($arParams["META_KEYWORDS"]);

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
        if(CModule::IncludeModule("nscache"))
            CExCacheM::SaveSectionCacheID($arParams["SECTION_CODE"], $cache_id, $curUrl);
    }

    if($arParams["IBLOCK_ID"] > 0) {
        if(strlen($arParams["SECTION_CODE"]) > 0 || $arParams["SECTION_ID"]) {
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE" => "Y",
                //">UF_QUANTITY" => 0
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

            $rsSection = CIBlockSection::GetList(false, $arFilter, false, $arSelect);
            if($arSection = $rsSection->GetNext()) {
                $arResult["SECTION"] = $arSection;
                $rsPath = CIBlockSection::GetNavChain($arResult["SECTION"]["IBLOCK_ID"], $arResult["SECTION"]["ID"]);
                while($arPath = $rsPath->GetNext()) {
                    $arResult["SECTION"]["PATH"][] = $arPath;
                }
            }
        }

        //EXECUTE
        if(is_array($arResult["SECTION"])) {
            $arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices("", $arParams["PRICE_CODE"]);
            $arFilter = array(
                "ACTIVE"=>"Y",
                "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
                "SECTION_ID" => $arResult["SECTION"]["ID"],
                ">CATALOG_QUANTITY" => 0
            );

            $arResult["ITEMS"] = array();
            $arSelect = array(
                "ID",
                "NAME",
                "CODE",
                "IBLOCK_ID",
                "PREVIEW_TEXT",
                // "IBLOCK_SECTION_ID",
                "DETAIL_PAGE_URL",
                "DETAIL_PICTURE"
            );
            $arSort = array(
                $arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"]
            );

            foreach($arResult["PRICES"] as $key => $value) {
                if($arParams["ELEMENT_SORT_FIELD"] == "price") {
                    $arSort = array(
                        "CATALOG_PRICE_".$value["ID"] => $arParams["ELEMENT_SORT_ORDER"]
                    );
                }
                $arSelect[] = $value["SELECT"];
            }

            $rsElements = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
            $arResult["ITEMS_COUNT"] = $rsElements->SelectedRowsCount();
            while($rsItem = $rsElements->GetNextElement()) {
                $arItem = $rsItem->GetFields();
                $arItem["P"] = $rsItem->GetProperties();

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
            $arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);

            //SORT FIELDS
            foreach ($arParams["ELEMENT_SORT_ARRAY_FIELDS"] as $sortKey => $sortValue) {
                $arSortElements = array();

                $trimPropertyCode = "";
                $trimPriceCode = "";
                if (strstr($sortValue, "CATALOG_PRICE_"))
                    $trimPriceCode = str_replace("property_CATALOG", "", $sortValue);
                elseif (strstr($sortValue, "property_"))
                    $trimPropertyCode  = str_replace("property_", "", $sortValue);

                if (strlen($trimPropertyCode) > 0)
                    $sortFieldName = $arResult["PROPERTIES"][$trimPropertyCode]["NAME"];
                elseif (strlen($trimPriceCode) > 0)
                    $sortFieldName = GetMessage("IBLOCK_SORT_price");
                else
                    $sortFieldName = GetMessage("IBLOCK_SORT_".$sortValue);

                if (strlen($sortFieldName) > 0) {
                    $sortOrder = "asc";
                    $arSortElements["CURRENT"] = "N";

                    if ($arParams["ELEMENT_SORT_FIELD"] == $sortValue) {
                        $arSortElements["CURRENT"] = "Y";
                        if ($arParams["ELEMENT_SORT_ORDER"] == "asc")
                            $sortOrder = "desc";
                    }
                    $arSortElements["FIELD"] = $sortValue;
                    $arSortElements["ORDER"] = $sortOrder;
                    $arSortElements["URL"] = $APPLICATION->GetCurPageParam("s=".$sortValue."&o=".$sortOrder, array("s", "o", "SECTION_ID", "SECTION_CODE"));
                    $arSortElements["NAME"] = $sortFieldName;

                    $arSortElementsRes[] = $arSortElements;
                }
            }
            $arResult["SORT_LINKS"] = $arSortElementsRes;
            $obCache->EndDataCache($arResult);
        } else {
            $obCache->AbortDataCache();
            @define("ERROR_404", "Y");
            CHTTP::SetStatus("404 Not Found");
        }
    } else {
        $obCache->AbortDataCache();
        @define("ERROR_404", "Y");
        CHTTP::SetStatus("404 Not Found");
    }
}

// список товаров в корзине пользователя
if(count($arResult["ITEMS"]) > 0)
    $arResult["IN_BASKET"] = CWsCatalogTools::GetBasketItems();

if(is_array($arResult["SECTION"]["PATH"])) {
    $pathName = array();
    foreach($arResult["SECTION"]["PATH"] as $arPath) {
        $url = $arPath["SECTION_PAGE_URL"];
        if($arPath["ID"] == $arResult["SECTION"]["ID"])
            $url = "";

        $APPLICATION->AddChainItem($arPath["NAME"], $url);
        $pathName[] = $arPath["NAME"];
    }

    // установка заголовка
    if($arParams["SET_TITLE"])
    {
        if(is_array($pathName))
            $sectionName = implode(" / ", $pathName);
        else
            $sectionName = $arResult["SECTION"]["NAME"];

        $title = $arResult["SECTION"]["NAME"];
        if(strlen($arParams["SEO_TITLE_PATTERN"]) > 0)
            $title = str_replace("#NAME#", $sectionName, $arParams["SEO_TITLE_PATTERN"]);

        $APPLICATION->SetTitle($title, $arTitleOptions);
        $APPLICATION->SetPageProperty("title", $arResult["SECTION"]["NAME"]);

        // устанавливаем мета-теги
        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $keywords = str_replace("#NAME#", $arResult["SECTION"]["NAME"], $arParams["SEO_KEYWORDS_PATTERN"]);
        if(isset($arResult["SECTION"][$arParams["META_KEYWORDS"]]))
        {
            $keywords = $arResult["SECTION"][$arParams["META_KEYWORDS"]];
            if(is_array($keywords))
                $keywords = implode(" ", $keywords);
        }

        if(strlen($keywords) > 0)
            $APPLICATION->SetPageProperty("keywords", $keywords);

        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $description = str_replace("#NAME#", $arResult["SECTION"]["NAME"], $arParams["SEO_DESCRIPTION_PATTERN"]);

        if(isset($arResult["SECTION"][$arParams["META_DESCRIPTION"]]))
        {
            $description = $arResult["SECTION"][$arParams["META_DESCRIPTION"]];
            if(is_array($description))
                $description = implode(" ", $description);
        }
        if(strlen($description) > 0)
            $APPLICATION->SetPageProperty("description", $description);
    }
}

$this->IncludeComponentTemplate();