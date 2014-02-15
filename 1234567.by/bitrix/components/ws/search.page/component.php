<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
Processing of received parameters
 *************************************************************************/
CModule::IncludeModule("search");
CModule::IncludeModule("catalog");
CModule::IncludeModule("currency");
CModule::IncludeModule("sale");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/phpmorphy/src/common.php");

global $USER;
/*if(!$USER->IsAdmin()){
   die("no access");
}*/
// Основные настройки
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600;

$arParams["IBLOCK_ID"] = 6;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["SECTION_CODE"] = trim($_REQUEST["SECTION_CODE"]);
$arParams["SECTION_ID"] = (int) $_REQUEST["SECTION_ID"];

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if($arParams["PAGE_ELEMENT_COUNT"] <= 0)
    $arParams["PAGE_ELEMENT_COUNT"] = 100;

// Сортировка
if(strlen($_REQUEST["s"]) > 0)
    $arParams["ELEMENT_SORT_FIELD"] = $_REQUEST["s"];
if(strlen($_REQUEST["o"]) > 0)
    $arParams["ELEMENT_SORT_ORDER"] = $_REQUEST["o"];

if(!in_array($arParams["ELEMENT_SORT_FIELD"], $arParams["ELEMENT_SORT_ARRAY_FIELDS"]))
    $arParams["ELEMENT_SORT_FIELD"] = "relevance";
$arParams["ELEMENT_SORT_ORDER"] = $arParams["ELEMENT_SORT_ORDER"] == "asc" ? "asc" : "desc";

// обработка постраничной навигации
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

$arParams["PAGE_RESULT_COUNT"] = 10;

if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
    $arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

# Поисковый запрос

global $DB;
//$_REQUEST["q"] = "воспитание школьников";


if(isset($_REQUEST["q"])) {
    $arParams["Q"] = trim($_REQUEST["q"]);
}
if(strlen($arParams["Q"]) > 1) {
    $arParams["Q"] = $DB->ForSql($arParams["Q"]);
    $arParams["~Q"] = htmlspecialcharsEx($arParams["Q"]);
    $arParams["Q"] = array($arParams["Q"]=> $arParams["Q"]);
    //$arParams["Q"] = stemming($arParams["Q"]);
    /*printHiddenObj($arWords);
    if(is_array($arWords)) {
        $arParams["Q"] = array();
        foreach($arWords as $w) {
            $w = preg_replace("/[^A-Za-z0-9а-яА-Я]/", "", $w);
            if(strlen($w) > 0)
                $arParams["Q"][$w] = $w;
        }
    }
    if(count($arParams["Q"]) == 0)
        $arParams["Q"] = "";*/
}

$arParams["IMAGES_MAX_HEIGHT"] = 51;
$arParams["IMAGES_MAX_WIDTH"] = 48;

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
$arParams["FULL_SEARCH"] = $arParams["FULL_SEARCH"] == "Y" ? "Y" : "N";

$arParams["IN_STOCK"] = $_REQUEST["in_stock"] == "N" ? "N" : "Y";

/*************************************************************************
    Work with cache
 *************************************************************************/

if(!CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
    //$this->AbortResultCache();
    ShowError(GetMessage("MODULE_NOT_INSTALLED"));
    return;
}

if($arParams["IBLOCK_ID"] > 0) {
    if(is_array($arParams["Q"])) {
        // информация инфоблока
        $arFilter = array(
            "ID" => $arParams["IBLOCK_ID"]
        );
        $rsIBlock = CIBlock::GetList(false, $arFilter);
        $arIBlock = $rsIBlock->Fetch();

        $arWords = array_keys($arParams["Q"]);
        $arIf = $arW = array();
        foreach($arWords as $w) {
            $w = $DB->ForSql($w);
            $arIf[] = " if(locate('".$w."', upper(ib.NAME)) > 0, 1, 0) ";
            $arW[] = " ib.NAME LIKE '%".$w."%'";
        }

        $selectFields = "";
        $having = "";
        $innerJoin = " INNER JOIN b_catalog_product cp ON cp.ID = ib.ID ";


        if($arParams["FULL_SEARCH"] == "Y") {
            $selectFields = "ib.PREVIEW_TEXT, ";
        }
        $selectFields .= " cp.QUANTITY, ";

        if($arParams["IN_STOCK"] == "Y") {
            $having = " HAVING cp.QUANTITY > 0 ";
        }
        if(count($arW) > 0) {
            //ORDER BY TITLE_RANK DESC, ib.SHOW_COUNTER DESC
            $sortOrder = $priceLeftJoin = $priceWhere = "";

            switch($arParams["ELEMENT_SORT_FIELD"]) {
                case "name":
                    $sortOrder = " ib.NAME ";
                break;
                case "shows":
                    $sortOrder = " ib.SHOW_COUNTER ";
                break;
                case "price":
                    if(intval($arParams["PRICE_CODE"]) > 0) {
                        $priceLeftJoin = " INNER JOIN b_catalog_price cprice ON cprice.PRODUCT_ID = ib.ID ";
                        $priceWhere = " AND cprice.CATALOG_GROUP_ID = ".$arParams["PRICE_CODE"]." AND cprice.PRICE > 0 ";
                        $sortOrder = " cprice.PRICE ";
                    } else {
                        $sortOrder = " TITLE_RANK ";
                    }
                    break;
                default:
                    $sortOrder = " TITLE_RANK ";
                    break;
            }

            $searchSql = "
                SELECT ib.ID, ib.NAME, ib.DETAIL_PICTURE, ib.CODE, ibs.CODE SECTION_CODE, $selectFields
                    ( ".implode(" + ", $arIf)." ) as TITLE_RANK
                FROM b_iblock_element ib
                ".$innerJoin."
                LEFT JOIN b_iblock_section ibs ON ibs.ID = ib.IBLOCK_SECTION_ID
                ".$priceLeftJoin."
                WHERE ((".implode(") OR (", $arW).")) and ib.IBLOCK_ID = ".$arParams["IBLOCK_ID"]." AND ibs.ID > 0
                ".$priceWhere."
                ".$having."
            ";

            $sortOrder .= $arParams["ELEMENT_SORT_ORDER"]." ";
            $searchSql .= " ORDER BY ".$sortOrder;

            if($arParams["FULL_SEARCH"] == "N")
                $searchSql .= "LIMIT ".$arParams["PAGE_RESULT_COUNT"];
        }

        $rsSearch = $DB->Query($searchSql);
        if($arParams["FULL_SEARCH"] == "Y")
            $rsSearch->NavStart($arParams["PAGE_ELEMENT_COUNT"]);

        while($arItem = $rsSearch->GetNext()) {
            foreach($arWords as $w) {
                $arItem["NAME"] = MatchHighlight($w, $arItem["NAME"]);
            }

            if($arItem["DETAIL_PICTURE"] > 0) {
                // изображения
                $arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
                //printObj($arItem);

                $mode = "width";

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
            }
            $arItem["DETAIL_PAGE_URL"] = str_replace(array("#ELEMENT_CODE#", "#SECTION_CODE#"), array($arItem["CODE"], $arItem["SECTION_CODE"]), $arIBlock["DETAIL_PAGE_URL"]);

            $rsPrice = CPrice::GetList(array(),
                array(
                    "PRODUCT_ID" => $arItem["ID"],
                    "CATALOG_GROUP_ID" => $arParams["PRICE_CODE"]
                )
            );
            if($arPrice = $rsPrice->Fetch()) {
                $arPrice["VALUE"] = $arPrice["PRICE"];
                $arPrice["PRINT_VALUE"] = SaleFormatCurrency($arPrice["PRICE"], $arPrice["CURRENCY"]);
                $arPrice["DISCOUNT_VALUE"] = $arPrice["VALUE"];

                $arPrice["USD_VALUE"] = CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], "USD");
                $arPrice["PRINT_USD_VALUE"] = SaleFormatCurrency($arPrice["USD_VALUE"], "USD");

                $arItem["PRICES"] = $arPrice;
            }

            $arResult["ITEMS"][] = $arItem;
        }

        if($arParams["FULL_SEARCH"] == "Y") {
            $arResult["NAV_STRING"] = $rsSearch->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);

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
        }
    }
}

if($arParams["FULL_SEARCH"] == "Y" && count($arResult["ITEMS"]) > 0) {
    // список товаров в корзине пользователя
    $arResult["IN_BASKET"] = CWsCatalogTools::GetBasketItems();
}

$this->IncludeComponentTemplate();