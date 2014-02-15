<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
	//добавление review_picture


	
	foreach($arResult["SEARCH"] as $num=>$item){
	
		$arSelect = Array("DETAIL_PICTURE");
		$arFilter = Array("ID"=>$item["ITEM_ID"], "ACTIVE"=>"Y");
		$dbres = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		$arres=$dbres->Fetch();
		
		CModule::IncludeModule("catalog");
				
		
		$cat_arres=CCatalogProduct::GetByID($item["ITEM_ID"]);
		$arResult["SEARCH"][$num]["QUANTITY"]=$cat_arres["QUANTITY"];
		$rsPrice = CPrice::GetList(array(), array( "PRODUCT_ID" => $item["ITEM_ID"] ) );
		if($arPrice=$rsPrice->Fetch()){

			$arPrice["VALUE"] = $arPrice["PRICE"];
			$arPrice["PRINT_VALUE"] = SaleFormatCurrency($arPrice["PRICE"], $arPrice["CURRENCY"]);
			$arPrice["DISCOUNT_VALUE"] = $arPrice["VALUE"];

			$arPrice["USD_VALUE"] = CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], "USD");
			$arPrice["PRINT_USD_VALUE"] = SaleFormatCurrency($arPrice["USD_VALUE"], "USD");

			$arResult["SEARCH"][$num]["PRICE"]=$arPrice["VALUE"];
			$arResult["SEARCH"][$num]["PRICES"] = $arPrice;
            
		}
		
		//$dbres = CIBlockElement::GetByID($item["ID"]);
/*
		echo "<pre>";
		echo var_dump($arres);
		echo var_dump($cat_arres);
		echo "</pre>";
*/
		$pic_src=CFile::ResizeImageGet($arres["DETAIL_PICTURE"], array('width'=>75, 'height'=> 75), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		//$pic_src= CFile::GetPath($arres["DETAIL_PICTURE"]);
		$arResult["SEARCH"][$num]["PICTURE_SRC"]=$pic_src['src'];
	}
	
	// вывод элементов только в наличии
	if($arParams["IN_STOCK"]=="Y"){
		$active_prod=array();
		foreach($arResult["SEARCH"] as $item){
			if ($item["QUANTITY"]>0){
				$active_prod[]=$item;
			}
		}
		$arResult["SEARCH"]=$active_prod;
	}
	// вывод элементов только в наличии
	
    $arResult["IN_BASKET"] = CWsCatalogTools::GetBasketItems();
	
	
	
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
				else $sortOrder = "asc";
			}
			$arSortElements["FIELD"] = $sortValue;
			$arSortElements["ORDER"] = $sortOrder;
			$arSortElements["URL"] = $APPLICATION->GetCurPageParam("f=".$sortValue."&o=".$sortOrder, array("f", "o", "SECTION_ID", "SECTION_CODE"));
			$arSortElements["NAME"] = $sortFieldName;

			$arSortElementsRes[] = $arSortElements;
		}
	}

	$arResult["SORT_LINKS"] = $arSortElementsRes;
	if($_REQUEST['f']){
	$arSeaarch=$arResult["SEARCH"];
	$tmp=array();
	foreach($arSeaarch as &$item){
		$tmp[]=&$item[$_REQUEST['f']];
	}
	if($_REQUEST['o']=="desc"){
		array_multisort($tmp, SORT_DESC, $arSeaarch);
	}
	else array_multisort($tmp, SORT_ASC, $arSeaarch);
	$arResult["SEARCH"]=$arSeaarch;
}

?>