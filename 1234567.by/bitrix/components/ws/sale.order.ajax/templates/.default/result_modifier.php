<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(count($arResult["BASKET_ITEMS"]) > 0) {
    $arParams["MAIN_CURRENCY"] = "BYR";
    $arParams["SEC_CURRENCY"] = "USD";

    foreach($arResult["BASKET_ITEMS"] as &$arItem) {
        $arPrice = array(
            array(
                "CURRENCY" => $arItem["CURRENCY"],
                "VALUE" => $arItem["PRICE"],
                "PRINT_VALUE" => $arItem["PRICE_FORMATED"]
            )
        );
        $arItem["PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);

        $arItem["TOTAL_PRICE"] = $arItem["PRICE"] * $arItem["QUANTITY"];
        $arPrice = array(
            array(
                "CURRENCY" => $arItem["CURRENCY"],
                "VALUE" => $arItem["TOTAL_PRICE"],
                "PRINT_VALUE" => SaleFormatCurrency($arItem["TOTAL_PRICE"], $arItem["CURRENCY"])
            )
        );
        $arItem["TOTAL_PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);
        $basketCurrency = $arItem["CURRENCY"];
    }

    $arPrice = array(
        array(
            "CURRENCY" => $basketCurrency,
            "VALUE" => $arResult["allSum"],
            "PRINT_VALUE" => $arResult["allSum_FORMATED"]
        )
    );
    $arResult["TOTAL_PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);
}
//printObj($arResult);