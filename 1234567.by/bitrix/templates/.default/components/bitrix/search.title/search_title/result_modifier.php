<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//You may customize user card fields to display
$arResult['USER_PROPERTY'] = array(
	"UF_DEPARTMENT",
);

//Code below searches for appropriate icon for search index item.
//All filenames should be lowercase.

//1
//Check if index item is information block element with property DOC_TYPE set.
//This property should be type list and we'll take it's values XML_ID as parameter
//iblock_doc_type_<xml_id>.png

//2
//When no such fle found we'll check for section attributes
//iblock_section_<code>.png
//iblock_section_<id>.png
//iblock_section_<xml_id>.png

//3
//Next we'll try to detect icon by "extention".
//where extension is all a-z between dot and end of title
//iblock_type_<iblock type id>_<extension>.png

//4
//If we still failed. Try to match information block attributes.
//iblock_iblock_<code>.png
//iblock_iblock_<id>.png
//iblock_iblock_<xml_id>.png

//5
//If indexed item is section when checkj for
//iblock_section.png
//If it is an element when chek for
//iblock_element.png

//6
//If item belongs to main module (static file)
//when check is done by it's extention
//main_<extention>.png

//7
//For blog module we'll check if icon for post or user exists
//blog_post.png
//blog_user.png

//8, 9 and 10
//forum_message.png
//intranet_user.png
//socialnetwork_group.png

//11
//In case we still failed to find an icon
//<module_id>_default.png

//12
//default.png

$arIBlocks = array();

$image_path = $this->GetFolder()."/images/";
$abs_path = $_SERVER["DOCUMENT_ROOT"].$image_path;

$arResult["SEARCH"] = array();
foreach($arResult["CATEGORIES"] as $category_id => $arCategory)
{
	foreach($arCategory["ITEMS"] as $i => $arItem)
	{
		if(isset($arItem["ITEM_ID"]))
			$arResult["SEARCH"][] = &$arResult["CATEGORIES"][$category_id]["ITEMS"][$i];
	}
}

foreach($arResult["SEARCH"] as $i=>$arItem)
{
	
	$tmp_res=CIBlockElement::GetByID( $arItem["ITEM_ID"])->Fetch();
	$tmp_img=CFile::ResizeImageGet($tmp_res["DETAIL_PICTURE"], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	
	$arResult["SEARCH"][$i]["ICON"] = $tmp_img["src"];

		CModule::IncludeModule("catalog");
				
		
		$rsPrice = CPrice::GetList(array(), array( "PRODUCT_ID" => $arItem["ITEM_ID"] ) );
		if($arPrice=$rsPrice->Fetch()){

			$arPrice["VALUE"] = $arPrice["PRICE"];
			$arPrice["PRINT_VALUE"] = SaleFormatCurrency($arPrice["PRICE"], $arPrice["CURRENCY"]);
			$arPrice["DISCOUNT_VALUE"] = $arPrice["VALUE"];

			$arPrice["USD_VALUE"] = CCurrencyRates::ConvertCurrency($arPrice["VALUE"], $arPrice["CURRENCY"], "USD");
			$arPrice["PRINT_USD_VALUE"] = SaleFormatCurrency($arPrice["USD_VALUE"], "USD");

			$arResult["SEARCH"][$i]["PRICE"]=$arPrice["VALUE"];
			$arResult["SEARCH"][$i]["PRICES"] = $arPrice;
            
		}
	
}


?>