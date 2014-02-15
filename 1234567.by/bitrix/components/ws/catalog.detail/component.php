<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
    Processing of received parameters
 *************************************************************************/
CModule::IncludeModule("iblock");

// Основные настройки
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000;

// время кеширования цен и остатков
if(!isset($arParams["CACHE_TIME_PRICE"]))
    $arParams["CACHE_TIME_PRICE"] = 3600;
$arParams["CACHE_TYPE"] = "N";

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["SECTION_CODE"] = trim($_REQUEST["SECTION_CODE"]);
$arParams["SECTION_ID"] = (int) $_REQUEST["SECTION_ID"];

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = trim($arParams["ELEMENT_CODE"]);

// цены
if(!is_array($arParams["PRICE_CODE"]))
    $arParams["PRICE_CODE"] = array();
$arParams["MAIN_CURRENCY"] = "BYR";
$arParams["SEC_CURRENCY"] = "USD";

// SEO
// Заголовок браузера
$arParams["BROWSER_TITLE"] = trim($arParams["BROWSER_TITLE"]);
// Мета-описание
$arParams["META_DESCRIPTION"] = trim($arParams["META_DESCRIPTION"]);
// Мета-ключевые слова
$arParams["META_KEYWORDS"] = trim($arParams["META_KEYWORDS"]);

// корзина
$arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
if(strlen($arParams["BASKET_URL"])<=0)
    $arParams["BASKET_URL"] = "/personal/basket.php";

// изображения
$arParams["IMAGES_SMALL_MAX_HEIGHT"] = intval($arParams["IMAGES_SMALL_MAX_HEIGHT"]);
if($arParams["IMAGES_SMALL_MAX_HEIGHT"] == 0)
    $arParams["IMAGES_SMALL_MAX_HEIGHT"] =  54;
$arParams["IMAGES_SMALL_MAX_WIDTH"] = intval($arParams["IMAGES_SMALL_MAX_WIDTH"]);
if($arParams["IMAGES_SMALL_MAX_WIDTH"] == 0)
    $arParams["IMAGES_SMALL_MAX_WIDTH"] =  54;

$arParams["IMAGES_MIDDLE_MAX_HEIGHT"] = intval($arParams["IMAGES_MIDDLE_MAX_HEIGHT"]);
if($arParams["IMAGES_MIDDLE_MAX_HEIGHT"] == 0)
    $arParams["IMAGES_MIDDLE_MAX_HEIGHT"] =  300;
$arParams["IMAGES_MIDDLE_MAX_WIDTH"] = intval($arParams["IMAGES_MIDDLE_MAX_WIDTH"]);
if($arParams["IMAGES_MIDDLE_MAX_WIDTH"] == 0)
    $arParams["IMAGES_MIDDLE_MAX_WIDTH"] =  300;

$arParams["IMAGES_BIG_MAX_HEIGHT"] = intval($arParams["IMAGES_BIG_MAX_HEIGHT"]);
if($arParams["IMAGES_BIG_MAX_HEIGHT"] == 0)
    $arParams["IMAGES_BIG_MAX_HEIGHT"] =  600;
$arParams["IMAGES_BIG_MAX_WIDTH"] = intval($arParams["IMAGES_BIG_MAX_WIDTH"]);
if($arParams["IMAGES_BIG_MAX_WIDTH"] == 0)
    $arParams["IMAGES_BIG_MAX_WIDTH"] =  800;

$arParams["NO_PHOTO_SRC"] = "/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png";
/*************************************************************************
    Work with cache
 *************************************************************************/
$obCache = new CPHPCache;
$cache_id = $this->GetCacheID();

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
    if(strlen($arParams["ELEMENT_CODE"]) > 0) {
        $elCode = $arParams["ELEMENT_CODE"];
    } elseif($arParams["ELEMENT_ID"] > 0)
        $elCode = $arParams["ELEMENT_ID"];
    if(CModule::IncludeModule("nscache"))
        CExCacheM::SaveElCacheID($elCode, $cache_id, "", "EL");

    if($arParams["IBLOCK_ID"] > 0) {
        if(strlen($arParams["SECTION_CODE"]) > 0 || $arParams["SECTION_ID"]) {
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
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
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE"    => "Y"
            );

            if(strlen($arParams["ELEMENT_CODE"]) > 0)
                $arFilter["CODE"] = $arParams["ELEMENT_CODE"];
            elseif($arParams["ELEMENT_ID"] > 0)
                $arFilter["ID"] = $arParams["ELEMENT_ID"];

            $arSelect = array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "ACTIVE",
                "CODE",
                "DETAIL_PICTURE",
                "PREVIEW_TEXT",
                "DETAIL_TEXT"
                //"PROPERTY_*"
            );

            $resElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=> "1"), $arSelect);
            if($rsElement = $resElement->GetNextElement())
            {
                $arElement = $rsElement->GetFields();
                // изображения
                if(intval($arElement["DETAIL_PICTURE"]) > 0)
                {
                    $arElement["DETAIL_PICTURE"] = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
                }
                $trim = true;
                //проверка на отсуствия фото..фраза для проверки вхождения в DESCRIPTION прописана в init.php
                if(!is_array($arElement["DETAIL_PICTURE"]) || (stripos($arElement["DETAIL_PICTURE"]["DESCRIPTION"],NO_FOTO_IMAGES)!==FALSE))
                {
                    $trim = false;
                    $arElement["DETAIL_PICTURE"]["SRC"] = $arParams["NO_PHOTO_SRC"];
                }


                $arImages = GetResizedImages(
                    $arElement["DETAIL_PICTURE"],
                    array(
                        "WIDTH"=>$arParams["IMAGES_SMALL_MAX_WIDTH"],
                        "HEIGHT"=>$arParams["IMAGES_SMALL_MAX_HEIGHT"]
                    ),
                    array(
                        "WIDTH"=>$arParams["IMAGES_MIDDLE_MAX_WIDTH"],
                        "HEIGHT"=>$arParams["IMAGES_MIDDLE_MAX_HEIGHT"]
                    ),
                    array(
                        "WIDTH"=>$arParams["IMAGES_BIG_MAX_WIDTH"],
                        "HEIGHT"=>$arParams["IMAGES_BIG_MAX_HEIGHT"]
                    ),
                    $trim,
                    $trim
                );

                $arElement["PREVIEW_PICTURE"] = $arImages["PREVIEW_PICTURE"];
                $arElement["MIDDLE_PICTURE"] = $arImages["MIDDLE_PICTURE"];
                $arElement["DETAIL_PICTURE"] = $arImages["DETAIL_PICTURE"];

                // доп. изображения
                $rsPhotos = CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"], array(), array("CODE" => "MORE_PHOTO"));
                while($rsPhoto = $rsPhotos->GetNext()) {
                    if(!is_array($arPhotos))
                        $arPhotos = array();

                    $arPhotos[] = $rsPhoto["VALUE"];
                }

                if(is_array($arPhotos))
                {
                    foreach($arPhotos as $imageID)
                    {
                        if(intval($imageID) > 0)
                        {
                            $arImage = CFile::GetFileArray($imageID);

                            $arImages = GetResizedImages(
                                $arImage,
                                array(
                                    "WIDTH"=>$arParams["IMAGES_SMALL_MAX_WIDTH"],
                                    "HEIGHT"=>$arParams["IMAGES_SMALL_MAX_HEIGHT"]
                                ),
                                array(
                                    "WIDTH"=>$arParams["IMAGES_MIDDLE_MAX_WIDTH"],
                                    "HEIGHT"=>$arParams["IMAGES_MIDDLE_MAX_HEIGHT"]
                                ),
                                array(
                                    "WIDTH"=>$arParams["IMAGES_BIG_MAX_WIDTH"],
                                    "HEIGHT"=>$arParams["IMAGES_BIG_MAX_HEIGHT"]
                                ),
                                true,
                                true
                            );
                            if(!isset($arImages["DETAIL_PICTURE"]))
                                $arImages["DETAIL_PICTURE"] = $arImage;

                            if(is_array($arImages))
                                $arElement["MORE_PHOTO"][] = $arImages;
                        }
                    }
                }

                $arResult["ITEM"] = $arElement;
                $obCache->EndDataCache($arResult);
            } else {
                $obCache->AbortDataCache();
                //ShowError(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
                @define("ERROR_404", "Y");
                CHTTP::SetStatus("404 Not Found");
            }
        } else {
            $obCache->AbortDataCache();
            //ShowError(GetMessage("CATALOG_ELEMENT_NOT_FOUND"));
            @define("ERROR_404", "Y");
            CHTTP::SetStatus("404 Not Found");
        }
    }
}

if($arResult["ITEM"]["ID"]) {
    // цены и остатки
    $cache_id = "price_".$cache_id;
    if($obCache->InitCache($arParams["CACHE_TIME_PRICE"], $cache_id, "/"))
    {
        $arResult["CATALOG"]  =  $obCache->GetVars();
        // printObj($arResult);
    }
    else
    {
        if($obCache->StartDataCache())
        {
            /**
             *  Сохранение id кеша элемента
             */
            if(strlen($arParams["ELEMENT_CODE"]) > 0) {
                $elCode = $arParams["ELEMENT_CODE"];
            } elseif($arParams["ELEMENT_ID"] > 0)
                $elCode = $arParams["ELEMENT_ID"];

            if(CModule::IncludeModule("nscache"))
                CExCacheM::SaveElCacheID($elCode, $cache_id, "", "EL_PRICE");

            // цены
            $arResultPrices = CIBlockPriceTools::GetCatalogPrices($arResult["ITEM"]["IBLOCK_ID"], $arParams["PRICE_CODE"]);

            $arFilter = array(
                "ID" =>  $arResult["ITEM"]["ID"],
                "IBLOCK_ID" => $arResult["ITEM"]["IBLOCK_ID"],
                "ACTIVE" => "Y"
            );

            $arSelect = array(
                "IBLOCK_ID",
                "ID",
            );

            if(!$arParams["USE_PRICE_COUNT"])
            {
                foreach($arResultPrices as $key => $value)
                {
                    $arSelect[] = $value["SELECT"];
                    //$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
                }
            }

            $rsElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=> "1"), $arSelect);
            if($resElement = $rsElement->GetNextElement())
            {
                $arElement = $resElement->GetFields();

                $arResult["PRICE_MATRIX"] = false;
                $arResult["PRICES"] = CIBlockPriceTools::GetItemPrices($arElement["IBLOCK_ID"], $arResultPrices, $arElement, $arParams['PRICE_VAT_INCLUDE']);

                if(is_array($arResult["PRICES"]) && CModule::IncludeModule("sale"))
                {
                    foreach($arResult["PRICES"] as $key=>$arPrice)
                    {
                        if($arParams["MAIN_CURRENCY"] != $arPrice["CURRENCY"])
                        {
                            $curValue =  CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], $arParams["MAIN_CURRENCY"]);
                            $curPrintValue = SaleFormatCurrency($curValue, $arParams["MAIN_CURRENCY"]);

                            $curDiscountValue =  CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_VALUE"], $arPrice["CURRENCY"], $arParams["MAIN_CURRENCY"]);
                            $curPrintDiscountValue = SaleFormatCurrency($curDiscountValue, $arParams["MAIN_CURRENCY"]);

                            $arResult["PRICES"][$key]["CURRENCY"] = $arParams["MAIN_CURRENCY"];
                            $arResult["PRICES"][$key]["VALUE"] = $curValue;
                            $arResult["PRICES"][$key]["PRINT_VALUE"] = $curPrintValue;
                            $arResult["PRICES"][$key]["DISCOUNT_VALUE"] = $curDiscountValue;
                            $arResult["PRICES"][$key]["PRINT_DISCOUNT_VALUE"] = $curPrintDiscountValue;
                        }

                        $curValue =  CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], $arParams["SEC_CURRENCY"]);
                        $curPrintValue = SaleFormatCurrency($curValue, $arParams["SEC_CURRENCY"]);

                        $curDiscountValue =  CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_VALUE"], $arPrice["CURRENCY"], $arParams["SEC_CURRENCY"]);
                        $curPrintDiscountValue = SaleFormatCurrency($curDiscountValue, $arParams["SEC_CURRENCY"]);

                        $arResult["PRICES"][$key][$arParams["SEC_CURRENCY"]] = array(
                            "VALUE" => $curValue,
                            "PRINT_VALUE" => $curPrintValue,
                            "DISCOUNT_VALUE" => $curDiscountValue,
                            "PRINT_DISCOUNT_VALUE" => $curPrintDiscountValue
                        );
                    }
                }

                // наличие на складах
                $arFilter = array(
                    "PRODUCT_ID" => $arElement["ID"]
                );

                $arResult["CATALOG"]["STORES"] = array();
                $rsStores = CCatalogStoreProduct::GetList(false, $arFilter);
                while($arStore = $rsStores->GetNext()) {
                    $arResult["CATALOG"]["STORES"][] = $arStore;
                }

                $arResult["CATALOG"]["PRICES"] = $arResult["PRICES"];
                $arResult["CATALOG"]["CATALOG_QUANTITY"] = $arElement["CATALOG_QUANTITY"];
                $arResult["CATALOG"]["CAN_BUY"] = CIBlockPriceTools::CanBuy($arElement["IBLOCK_ID"], $arResultPrices, $arElement);
                $arResult["CATALOG"]["PRICE_TYPE"] = $arResultPrices;

                $obCache->EndDataCache($arResult["CATALOG"]);
            }
        }
    }

    // список товаров в корзине пользователя
    $arResult["IN_BASKET"] = CWsCatalogTools::GetBasketItems();

    // навигационная цепочка
    if(is_array($arResult["SECTION"]["PATH"])) {
        foreach($arResult["SECTION"]["PATH"] as $arPath) {
            $url = $arPath["SECTION_PAGE_URL"];
            if($arPath["ID"] == $arResult["SECTION"]["ID"])
                $url = str_replace("/s_", "/", $url);

            $APPLICATION->AddChainItem($arPath["NAME"], $url);
        }
    }

    // установка заголовка
    if($arParams["SET_TITLE"])
    {
        $title = $arResult["ITEM"]["NAME"];
        if(strlen($arParams["SEO_TITLE_PATTERN"]) > 0)
            $title = str_replace("#PRODUCT_NAME#", $arResult["ITEM"]["NAME"], $arParams["SEO_TITLE_PATTERN"]);
        $keyProps = $arResult["ITEM"]["PROPERTIES"];
        if(strlen($arParams["BROWSER_TITLE_TEMPLATE"]) > 0)
            $title = str_replace(array("#NAME#"), array($title), $arParams["BROWSER_TITLE_TEMPLATE"]);
        if(strlen($keyProps[$arParams["BROWSER_TITLE"]]["VALUE"]) > 0)
            $title = $keyProps[$arParams["BROWSER_TITLE"]]["VALUE"];

        $APPLICATION->SetTitle($title, $arTitleOptions);
        $APPLICATION->SetPageProperty("title", $arResult["ITEM"]["NAME"]);



        // устанавливаем мета-теги
        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $keywords = str_replace("#PRODUCT_NAME#", $arResult["ITEM"]["NAME"], $arParams["SEO_KEYWORDS_PATTERN"]);

        if(isset($arResult["ITEM"]["PROPERTIES"][$arParams["META_KEYWORDS"]]))
        {
            $keywords = $arResult["ITEM"]["PROPERTIES"][$arParams["META_KEYWORDS"]]["VALUE"];
            if(is_array($val))
                $keywords = implode(" ", $keywords);
        }
        if(strlen($keywords))
            $APPLICATION->SetPageProperty("keywords", $keywords);

        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $description = str_replace("#PRODUCT_NAME#", $arResult["ITEM"]["NAME"], $arParams["SEO_DESCRIPTION_PATTERN"]);

        if(isset($arResult["ITEM"]["PROPERTIES"][$arParams["META_DESCRIPTION"]]))
        {
            $description = $arResult["ITEM"]["PROPERTIES"][$arParams["META_DESCRIPTION"]]["VALUE"];
            if(is_array($val))
                $description = implode(" ", $description);
        }
        if(strlen($description) > 0)
            $APPLICATION->SetPageProperty("description", $description);
    }
    // инкрементация счетчика
    CIBlockElement::CounterInc($arResult["ITEM"]["ID"]);
}

$this->IncludeComponentTemplate();