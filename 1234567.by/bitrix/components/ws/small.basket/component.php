<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
 * Компонент small.basket v.1.0
 * Автор: vmakaed@gmail.com
 * 
 * Используется для проектов, в которых организован обмен со сторонними учетными системами. 
 * В данной версии в товарную позицию корзины можно поля NAME, PREVIEW_PICTURE, DETAIL_PAGE_URL. 
 * 
 */


// вывод описания компонента
if(function_exists('objDescr'))
{
    objDescr(
        "Компонент",
        "small.basket" ,
        "v.1.0",
        "vmakaed@gmail.com",
        '25.04.2011',
        'Используется для проектов, в которых организован обмен со сторонними учетными системами. 
         При этом необходимо вести управление контентом прямо на сайте, не затрагивая базу заказчика 
         В данной версии в товарную позицию корзины можно поля NAME, PREVIEW_PICTURE, DETAIL_PAGE_URL. '
    );
}


if(!CModule::IncludeModule("iblock"))
{
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return 0;
}
if(!CModule::IncludeModule("sale"))
{
    ShowError(GetMessage("SALE_MODULE_NOT_INSTALLED"));
    return 0;
}

/*************************************************************************
    Processing of received parameters
*************************************************************************/
$arParams["PATH_TO_ORDER"] = strlen($arParams["PATH_TO_ORDER"]) > 0 ? $arParams["PATH_TO_ORDER"] : "/personal/order/make/";
$arParams["PATH_TO_BASKET"] = strlen($arParams["PATH_TO_BASKET"]) > 0 ? $arParams["PATH_TO_BASKET"] : "/personal/basket.php";
$arParams["ITEM_ID"] = intval($_REQUEST["ITEM_ID"]);
$arParams["ACTION"] = strlen($_REQUEST["ACTION"]) > 0 ? $_REQUEST["ACTION"] : "READ";
$arParams["MAIN_CURRENCY"] = "USD";
$arParams["SECOND_CURRENCY"] = "BYR";

// в разделе оформления заказа и в корзине малая корзина не должна отображаться
/*$curPage = $APPLICATION->GetCurPage();
if($curPage == $arParams["PATH_TO_BASKET"] 
    || $curPage == $arParams["PATH_TO_ORDER"])
    return;
*/

foreach($arParams["SHOW_FIELDS"] as $key=>$showField)
{
    if(strlen($showField) > 0)
        $tmpFields[$key] = $showField;
}
if(is_array($tmpFields))
{
    $arParams["SHOW_FIELDS"] = $tmpFields;
    unset($tmpFields);
}

$arParams["SECTION_CURRENCY"] = array(
	"CASH" 		=> "BYR",
	"CREDIT"	=> "BYR",
	"NONCASH"	=> "BYR"
);
if(strlen($arParams["SECTION"]) <= 0) {
    $arParams["SECTION"] = "CASH";
}

/*************************************************************************
            Work
*************************************************************************/
if($arParams["ACTION"] == "DELETE" && $arParams["ITEM_ID"] > 0)
{
    // можем очищать только свою корзину
    $dbBasketItems = CSaleBasket::GetList(
        array("NAME" => "ASC"),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
        false,
        false,
        array("ID")
    );
    while ($arItems = $dbBasketItems->Fetch())
    {
        if($arItems["ID"] == $arParams["ITEM_ID"])
            CSaleBasket::Delete($arParams["ITEM_ID"]);
    }
}

$dbBasketItems = CSaleBasket::GetList(
        array("NAME" => "ASC"),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
        false,
        false,
        array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE")
    );
$totalSumm = 0;
// суммарная стоимость кредитных товаров
$totalPrice = 0;
$sectionProductCount = 0;

while ($arItems = $dbBasketItems->Fetch())
{
    if($arItems["CAN_BUY"] != "N" && $arItems["DELAY"] != "Y")
    {
        //свойства корзины. Необходимы для отображения кредитных товаров
        $arItems["PROPS"] = Array();

        $dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arItems["ID"]));
        while($arProp = $dbProp -> GetNext())
            $arItems["PROPS"][$arProp["CODE"]] = $arProp;

        // проверка на наличие товара на складе
        if(CModule::IncludeModule("catalog"))
            $arProduct = CCatalogProduct::GetByID($arItems["PRODUCT_ID"]);

        // стоимость с количеством
        if($arProduct["QUANTITY"] > 0) {
            if($arItems["CURRENCY"] != $arParams["MAIN_CURRENCY"]) {
                $arItems["PRICE"] = CCurrencyRates::ConvertCurrency($arItems["PRICE"], $arItems["CURRENCY"], $arParams["MAIN_CURRENCY"]);
                $arItems["CURRENCY"] = $arParams["MAIN_CURRENCY"];
            }

            $arItems["TOTAL_PRICE"] = $arItems["PRICE"] * $arItems["QUANTITY"];

            if(count($arItems["PROPS"]) > 0)
            {
                if($arParams["SECTION"] == $arItems["PROPS"][$arParams["SECTION"]]["CODE"])
                {
                    $sectionProductCount++;
                    $currency = $arParams["SECTION_CURRENCY"][$arParams["SECTION"]];
                    if($currency != $arItems["CURRENCY"])
                    {
                        $arItems["TOTAL_PRICE"] = CCurrencyRates::ConvertCurrency($arItems["TOTAL_PRICE"], $arItems["CURRENCY"], $currency);
                        $arItems["CURRENCY"] = $currency;
                        $arResult["CURRENCY"] = $arItems["CURRENCY"];
                    }
                    $totalSumm += $arItems["TOTAL_PRICE"];
                }
            }
            else
            {
                if($arParams["SECTION"] == "CASH")
                {
                    $sectionProductCount++;
                    $totalSumm += $arItems["TOTAL_PRICE"];
                    $arResult["CURRENCY"] = $arItems["CURRENCY"];
                }
            }

            $arItems["TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($arItems["TOTAL_PRICE"], $arItems["CURRENCY"]);
            $arItems["TOTAL_PRICE_RUB"] = CCurrencyRates::ConvertCurrency($arItems["TOTAL_PRICE"], $arItems["CURRENCY"], $arParams["SECOND_CURRENCY"]);
            $arItems["TOTAL_PRICE_RUB_FORMATED"] = SaleFormatCurrency($arItems["TOTAL_PRICE_RUB"], $arParams["SECOND_CURRENCY"]);


            $arItems["PRICE_FORMATED"] = SaleFormatCurrency($arItems["PRICE"], $arItems["CURRENCY"]);

            if(DoubleVal($arItems["DISCOUNT_PRICE"]) > 0)
            {
                $arItems["DISCOUNT_PRICE_PERCENT"] = $arItems["DISCOUNT_PRICE"]*100 / ($arItems["DISCOUNT_PRICE"] + $arItems["PRICE"]);
                $arItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItems["DISCOUNT_PRICE_PERCENT"], 0)."%";
            }
        }
        $arBasketItems[$arItems["PRODUCT_ID"]] = $arItems;

        // id для получения данных о связях
        $arIds[] = $arItems["PRODUCT_ID"];
    }
}

$arResult["ITEMS"] = $arBasketItems;
$arResult["SECTION_COUNT"] = $sectionProductCount;
//if($arResult["SECTION_COUNT"] == 0)
//	$arResult["SECTION_COUNT"] = count($arResult["ITEMS"]);

if(is_array($arBasketItems))
{
	// службы доставок
	$orderPriceUSD = CCurrencyRates::ConvertCurrency($totalSumm, $arResult["CURRENCY"], "USD");
	$rsDelivery = CSaleDelivery::GetList(
	    array(
	            "PRICE" => "ASC",
	        ),
	    array(
	            "LID" => SITE_ID,
	            "+<=ORDER_PRICE_FROM" => $orderPriceUSD,
	            "+>=ORDER_PRICE_TO" => $orderPriceUSD,
	            "ACTIVE" => "Y"
	        ),
	    false,
	    false,
	    array()
	);
	while($arDelivery = $rsDelivery->GetNext())
	{
		$rubPrice = CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], "BYR");
		$arResult["DELIVERY"] = array(
			"ID"			=> $arDelivery["ID"],
			"NAME"			=> $arDelivery["NAME"],
			"PRICE" 		=> $arDelivery["PRICE"],
			"RUB_PRICE" 	=> $rubPrice,
			"RUB_PRICE_FORMATED" => CurrencyFormat($rubPrice, "BYR"),
			"DESCRIPTION" 	=> $arDelivery["DESCRIPTION"]
		);
	}

	// итого за товары
    $arResult["PRODUCT_TOTAL_SUMM"] = $totalSumm;
    $arResult["PRODUCT_TOTAL_SUMM_FORMATED"] = SaleFormatCurrency($totalSumm, $arResult["CURRENCY"]);
	$arResult["PRODUCT_TOTAL_SUMM_RUB"] = CCurrencyRates::ConvertCurrency($arResult["PRODUCT_TOTAL_SUMM"], $arResult["CURRENCY"], $arParams["SECOND_CURRENCY"]);
	$arResult["PRODUCT_TOTAL_SUMM_RUB_FORMATED"] = SaleFormatCurrency($arResult["PRODUCT_TOTAL_SUMM_RUB"], $arParams["SECOND_CURRENCY"]);

	// итого за заказ
	$arResult["TOTAL_SUMM"] = $totalSumm + $arResult["DELIVERY"]["PRICE"];
    $arResult["TOTAL_SUMM_FORMATED"] = SaleFormatCurrency($arResult["TOTAL_SUMM"], $arResult["CURRENCY"]);
	$arResult["TOTAL_SUMM_RUB"] = CCurrencyRates::ConvertCurrency($arResult["TOTAL_SUMM"], $arResult["CURRENCY"], $arParams["SECOND_CURRENCY"]);
	$arResult["TOTAL_SUMM_RUB_FORMATED"] = SaleFormatCurrency($arResult["TOTAL_SUMM_RUB"], $arParams["SECOND_CURRENCY"]);
}
else
   $arResult["ERROR_MESSAGE"] = "SALE_EMPTY_BASKET";


$this->IncludeComponentTemplate();
?>