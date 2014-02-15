<?
/**
 * Обработчики событий
 */
AddEventHandler('main', 'OnEpilog', '_Check404Error', 1);
function _Check404Error()
{
    if (defined('ERROR_404') && ERROR_404=='Y' && !defined('ADMIN_SECTION'))
    {
        GLOBAL $APPLICATION;
        $APPLICATION->RestartBuffer();

        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/home/header.php';

        @define("MANUAL_404", "Y");
        require $_SERVER['DOCUMENT_ROOT'].'/404.php';
        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/home/footer.php';
    }
    //return true;
}


/*Version 0.3 2011-04-25*/
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "DoIBlockAfterSave");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "DoIBlockAfterSave");
AddEventHandler("catalog", "OnPriceAdd", "DoIBlockAfterSave");
AddEventHandler("catalog", "OnPriceUpdate", "DoIBlockAfterSave");
function DoIBlockAfterSave($arg1, $arg2 = false)
{
    $ELEMENT_ID = false;
    $IBLOCK_ID = false;
    $OFFERS_IBLOCK_ID = false;
    $OFFERS_PROPERTY_ID = false;
    if (CModule::IncludeModule('currency'))
        $strDefaultCurrency = CCurrency::GetBaseCurrency();

    //Check for catalog event
    if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0)
    {
        //Get iblock element
        $rsPriceElement = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arg2["PRODUCT_ID"],
            ),
            false,
            false,
            array("ID", "IBLOCK_ID")
        );
        if($arPriceElement = $rsPriceElement->Fetch())
        {
            $arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
            if(is_array($arCatalog))
            {
                //Check if it is offers iblock
                if($arCatalog["OFFERS"] == "Y")
                {
                    //Find product element
                    $rsElement = CIBlockElement::GetProperty(
                        $arPriceElement["IBLOCK_ID"],
                        $arPriceElement["ID"],
                        "sort",
                        "asc",
                        array("ID" => $arCatalog["SKU_PROPERTY_ID"])
                    );
                    $arElement = $rsElement->Fetch();
                    if($arElement && $arElement["VALUE"] > 0)
                    {
                        $ELEMENT_ID = $arElement["VALUE"];
                        $IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
                        $OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
                        $OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
                    }
                }
                //or iblock which has offers
                elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0)
                {
                    $ELEMENT_ID = $arPriceElement["ID"];
                    $IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
                    $OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
                    $OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
                }
                //or it's regular catalog
                else
                {
                    $ELEMENT_ID = $arPriceElement["ID"];
                    $IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
                    $OFFERS_IBLOCK_ID = false;
                    $OFFERS_PROPERTY_ID = false;
                }
            }
        }
    }
    //Check for iblock event
    elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0)
    {
        //Check if iblock has offers
        $arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
        if(is_array($arOffers))
        {
            $ELEMENT_ID = $arg1["ID"];
            $IBLOCK_ID = $arg1["IBLOCK_ID"];
            $OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
            $OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
        }
    }

    if($ELEMENT_ID)
    {
        static $arPropCache = array();
        if(!array_key_exists($IBLOCK_ID, $arPropCache))
        {
            //Check for MINIMAL_PRICE property
            $rsProperty = CIBlockProperty::GetByID("MINIMUM_PRICE", $IBLOCK_ID);
            $arProperty = $rsProperty->Fetch();
            if($arProperty)
                $arPropCache[$IBLOCK_ID] = $arProperty["ID"];
            else
                $arPropCache[$IBLOCK_ID] = false;
        }

        if($arPropCache[$IBLOCK_ID])
        {
            //Compose elements filter
            if($OFFERS_IBLOCK_ID)
            {
                $rsOffers = CIBlockElement::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => $OFFERS_IBLOCK_ID,
                        "PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
                    ),
                    false,
                    false,
                    array("ID")
                );
                while($arOffer = $rsOffers->Fetch())
                    $arProductID[] = $arOffer["ID"];

                if (!is_array($arProductID))
                    $arProductID = array($ELEMENT_ID);
            }
            else
                $arProductID = array($ELEMENT_ID);

            $minPrice = false;
            $maxPrice = false;
            //Get prices
            $rsPrices = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arProductID,
                )
            );
            while($arPrice = $rsPrices->Fetch())
            {
                if (CModule::IncludeModule('currency') && $strDefaultCurrency != $arPrice['CURRENCY'])
                    $arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);

                $PRICE = $arPrice["PRICE"];

                if($minPrice === false || $minPrice > $PRICE)
                    $minPrice = $PRICE;

                if($maxPrice === false || $maxPrice < $PRICE)
                    $maxPrice = $PRICE;
            }

            //Save found minimal price into property
            if($minPrice !== false)
            {
                CIBlockElement::SetPropertyValuesEx(
                    $ELEMENT_ID,
                    $IBLOCK_ID,
                    array(
                        "MINIMUM_PRICE" => $minPrice,
                        "MAXIMUM_PRICE" => $maxPrice,
                    )
                );
            }
        }
    }
}

AddEventHandler("iblock", "OnBeforeIBlockElementDelete", Array("CFileEvents", "clearScaledImages"));
class CFileEvents {
    function clearScaledImages($elID) {
        if($elID > 0) {
            $rsEl = CIBlockElement::GetList(false, array("ID" => $elID), false, array("nTopCount" => 1), array("DETAIL_PICTURE"));
            if($arEl = $rsEl->GetNext()) {
                if($arEl["DETAIL_PICTURE"] > 0) {
                    $arFile = CFile::GetFileArray($arEl["DETAIL_PICTURE"]);
                    if(is_array($arFile)) {
                        $arFile["SUBDIR"] = str_replace($arFile["MODULE_ID"]."/", "", $arFile["SUBDIR"]);
                        $pathToScale = $_SERVER["DOCUMENT_ROOT"]."/images/cache/".$arFile["SUBDIR"]."/*".$arFile["FILE_NAME"];
                        $files = glob($pathToScale);
                        if(is_array($files)) {
                            foreach($files as $fileName) {
                                @unlink($fileName);
                            }
                        }
                    }
                }
            }
        }
    }
}