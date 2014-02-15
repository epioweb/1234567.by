<?
/**
 * Вспомогательные инструменты для каталога
 * @athor vmakaed@gmail.com
 */

class CWsCatalogTools {
    public  static function GetSectionUrl($arSection) {
        if(isset($arSection["LEFT_MARGIN"]) && isset($arSection["RIGHT_MARGIN"])) {
            if($arSection["RIGHT_MARGIN"] - $arSection["LEFT_MARGIN"] == 1)
                return str_replace("/s_", "/", $arSection["SECTION_PAGE_URL"]);
            else
                return $arSection["SECTION_PAGE_URL"];
        }
    }

    /**
     * Функция для конвертации цены в другую валюту
     * @val array $arPrices - массив цен
     * @val array $arParams - входные параметры
     *
     * @return array
     */
    public static function ConvertPrice($arPrices, $arParams)
    {
        if(CModule::IncludeModule("sale"))
        {
            if(is_array($arPrices))
            {
                foreach($arPrices as $key=>$arPrice)
                {
                    if($arParams["MAIN_CURRENCY"] != $arPrice["CURRENCY"])
                    {
                        $curValue = $arPrice["VALUE"];
                        if($arPrice["CURRENCY"] != $arParams["MAIN_CURRENCY"])
                            $curValue =  CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], $arParams["MAIN_CURRENCY"]);

                        $curPrintValue = SaleFormatCurrency($curValue, $arParams["MAIN_CURRENCY"]);

                        $curDiscountValue =  CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_VALUE"], $arPrice["CURRENCY"], $arParams["MAIN_CURRENCY"]);
                        $curPrintDiscountValue = SaleFormatCurrency($curDiscountValue, $arParams["MAIN_CURRENCY"]);

                        $arPrices[$key]["CURRENCY"] = $arParams["MAIN_CURRENCY"];
                        $arPrices[$key]["VALUE"] = $curValue;
                        $arPrices[$key]["PRINT_VALUE"] = $curPrintValue;
                        $arPrices[$key]["DISCOUNT_VALUE"] = $curDiscountValue;
                        $arPrices[$key]["PRINT_DISCOUNT_VALUE"] = $curPrintDiscountValue;
                    }

                    $curValue =  CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], $arParams["SEC_CURRENCY"]);
                    $curPrintValue = SaleFormatCurrency($curValue, $arParams["SEC_CURRENCY"]);

                    $curDiscountValue =  CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_VALUE"], $arPrice["CURRENCY"], $arParams["SEC_CURRENCY"]);
                    $curPrintDiscountValue = SaleFormatCurrency($curDiscountValue, $arParams["SEC_CURRENCY"]);

                    $arPrices[$key][$arParams["SEC_CURRENCY"]] = array(
                        "VALUE" => $curValue,
                        "PRINT_VALUE" => $curPrintValue,
                        "DISCOUNT_VALUE" => $curDiscountValue,
                        "PRINT_DISCOUNT_VALUE" => $curPrintDiscountValue
                    );
                }
                return $arPrices;
            }
        }

        return false;
    }

    /**
     * Возвращает список товаров в корзине
     * @return array
     */
    public static function GetBasketItems() {
        $arItems = array();
        if(CModule::IncludeModule("sale")) {
            $dbBasketItems = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                array(
                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL"
                ),
                false,
                false,
                array("ID", "PRODUCT_ID")
            );

            while($arItem = $dbBasketItems->Fetch())
                $arItems[] = $arItem["PRODUCT_ID"];
        }

        return $arItems;
    }
}