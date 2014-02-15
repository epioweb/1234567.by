<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$toJson = array(
	"TOTAL_SUM" 			=> $arResult["TOTAL_SUMM_FORMATED"],
	"TOTAL_SUM_RUB" 		=> $arResult["TOTAL_SUMM_RUB_FORMATED"],
	"PRODUCT_TOTAL_SUM" 	=> $arResult["PRODUCT_TOTAL_SUMM_FORMATED"],
	"PRODUCT_TOTAL_SUM_RUB" => $arResult["PRODUCT_TOTAL_SUMM_RUB_FORMATED"],
	"DELIVERY" 				=> $arResult["DELIVERY"],
	"TOTAL_COUNT" 			=> count($arResult["ITEMS"]),
	"COUNT" 				=> (int) $arResult["SECTION_COUNT"],
	"ITEMS" 				=> $arResult["ITEMS"],
	"SECTION" 				=> $arParams["SECTION"],
	"PATH_TO_BASKET"        => $arParams["PATH_TO_BASKET"]
);?>
<?$APPLICATION->RestartBuffer();?>
<?=json_encode($toJson)?>