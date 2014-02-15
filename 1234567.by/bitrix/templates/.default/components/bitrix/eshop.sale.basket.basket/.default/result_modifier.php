<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0) {
    $arParams["MAIN_CURRENCY"] = "BYR";
    $arParams["SEC_CURRENCY"] = "USD";

    foreach($arResult["ITEMS"]["AnDelCanBuy"] as &$arItem) {
        $arPrice = array(
            array(
                "CURRENCY" => $arItem["CURRENCY"],
                "VALUE" => $arItem["PRICE"]
            )
        );
        $arItem["PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);

        $arItem["TOTAL_PRICE"] = $arItem["PRICE"] * $arItem["QUANTITY"];
        $arPrice = array(
            array(
                "CURRENCY" => $arItem["CURRENCY"],
                "VALUE" => $arItem["TOTAL_PRICE"]
            )
        );
        $arItem["TOTAL_PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);
        $basketCurrency = $arItem["CURRENCY"];
    }

    $arPrice = array(
        array(
            "CURRENCY" => $basketCurrency,
            "VALUE" => $arResult["allSum"]
        )
    );
    $arResult["TOTAL_PRICES"] = CWsCatalogTools::ConvertPrice($arPrice, $arParams);
}
//printObj($arResult);