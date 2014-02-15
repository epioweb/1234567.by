<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
foreach($arResult as &$arItem) {
    // Подсчет общего количества товаров
    if($arItem["PARAMS"]["COUNTER"] == "COUNTER" && $arItem["PARAMS"]["IBLOCK_ID"] > 0) {
        $totalCnt = 0;
        $arFilter = array(
            "IBLOCK_ID" => $arItem["PARAMS"]["IBLOCK_ID"],
            "ACTIVE"    => "Y",
            ">UF_QUANTITY" => 0,
            "DEPTH_LEVEL" => 1
        );
        $rsSections = CIBlockSection::GetList(false, $arFilter, false, array("IBLOCK_ID", "ID", "UF_QUANTITY"));
        while($arSection = $rsSections->GetNext()) {
            $totalCnt += $arSection["UF_QUANTITY"];
        }

        $arItem["PRODUCT_COUNT"] = $totalCnt;
    }
}