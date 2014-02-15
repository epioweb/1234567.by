<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$arParams["ACTION_VARIABLE"] = "ACTION";
$arParams["PRODUCT_ID_VARIABLE"] = "id";
$arParams["QUANTITY_VARIABLE"] = "quantity";
// Разделы корзины
$arSections = array(
    "CASH",
    "NONCASH",
    "CREDIT"
);

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
{
    if (array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
    {
        $action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);
        $productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
        $priceID = intval($_REQUEST["PRICE_ID"]);
        if($priceID == 0)
            $priceID = 6;

        $quantity = intval($_REQUEST[$arParams["QUANTITY_VARIABLE"]]);
        $bResult = false;
        $category = "CASH";

        // credit
        $credit = trim($_REQUEST[$arParams["CREDIT_VARIABLE"]]) == "Y" ? "Y" : "N";
        $creditTerm = intval($_REQUEST["CREDIT_TERM"]);
        $initPayment = intval($_REQUEST["INIT_PAYMENT"]);



        // print_admin($productName);
        if($productID == 0)
        {
            $arResponse = array(
                "ERROR" => "PRODUCT_ID_EMPTY",
                "QUANTITY" => "0"
            );
            $APPLICATION->RestartBuffer();
            echo json_encode($arResponse);
            return;
        }

        if($action == "ADD2BASKET" || $action == "BUY")
        {
            // проверка на наличие товара на складе
            if(CModule::IncludeModule("catalog"))
                $arProduct = CCatalogProduct::GetByID($productID);

            if(intval($arProduct["QUANTITY"]) >= $quantity)
            {
                // если товар уже в корзине, обновляем его количество
                $dbBasketItems = CSaleBasket::GetList(
                    array(
                        "NAME" => "ASC",
                        "ID" => "ASC"
                    ),
                    array(
                        "FUSER_ID" 	=> CSaleBasket::GetBasketUserID(),
                        "ORDER_ID" 	=> "NULL",
                        "PRODUCT_ID"=> $productID
                    ),
                    false,
                    false,
                    array()
                );
                if($arDbItem = $dbBasketItems->GetNext())
                {
                    // определение категории корзины, к которой относится товар
                    $category = "CASH";
                    $arFilter = array(
                        "BASKET_ID" => $arDbItem["ID"],
                        "@CODE"		=> $arSections
                    );

                    $rsSectionProp = CSaleBasket::GetPropsList(array("SORT" => "ASC"), $arFilter);
                    if ($arSectionProp = $rsSectionProp->GetNext())
                        $category = $arSectionProp["CODE"];

                    if($category != "BTB")
                    {
                        $arFields = array(
                            "QUANTITY" => $quantity
                        );
                        $bResult = CSaleBasket::Update($arDbItem["ID"], $arFields);
                    }
                }
                else
                {
                    // получаем данные по цене
                    $db_res = CPrice::GetList(
                        array(),
                        array(
                            "PRODUCT_ID" => $productID,
                            "CATALOG_GROUP_ID" => $priceID
                        )
                    );
                    if ($arPrice = $db_res->Fetch())
                    {
                        if($arPrice["CAN_BUY"] == "Y")
                        {
                            // получаем информацию по продукту
                            $rsElement = CIBlockElement::GetList(array("ID"=>"ASC"), array("ID"=>$productID), false, array("nTopCount" => 1), array("NAME", "XML_ID"));
                            if($arElement = $rsElement->GetNext())
                            {
                                $arFields = array(
                                    "PRODUCT_ID" => $productID,
                                    "PRODUCT_PRICE_ID" => $arPrice["ID"],
                                    "PRICE" => $arPrice["PRICE"],
                                    "CURRENCY" => $arPrice["CURRENCY"],
                                    "QUANTITY" => $quantity,
                                    "LID" => LANG,
                                    "CAN_BUY" => "Y",
                                    "NAME" => $arElement["NAME"],
                                    "PRODUCT_XML_ID" => $arElement["XML_ID"]
                                );
                                $bResult = CSaleBasket::Add($arFields);
                            }
                            else
                            {
                                $arResponse = array(
                                    "ERROR" => "NO PERMISIONS",
                                    "QUANTITY" => "0"
                                );
                            }
                        }
                    }
                    else{
                        $arResponse = array(
                            "ERROR" => "PRICE NOT FOUND",
                            "QUANTITY" => "0"
                        );
                    }
                }
            }
            else
            {
                $arResponse = array(
                    "ERROR" => "OUT_OF_STOCK",
                    "QUANTITY" => $arProduct["QUANTITY"]
                );

                $dbBasketItems = CSaleBasket::GetList(
                    array(
                        "NAME" => "ASC",
                        "ID" => "ASC"
                    ),
                    array(
                        "FUSER_ID" 	=> CSaleBasket::GetBasketUserID(),
                        "ORDER_ID" 	=> "NULL",
                        "PRODUCT_ID"=> $arProduct["ID"]
                    ),
                    false,
                    false,
                    array()
                );

                if($arDbItem = $dbBasketItems->GetNext())
                    $arResponse["TOTAL_IN_BASKET"] = (int) $arDbItem["QUANTITY"];
            }
        }
        elseif($action == "DELETE")
        {
            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "FUSER_ID" 	=> CSaleBasket::GetBasketUserID(),
                    "ORDER_ID" 	=> "NULL",
                    "PRODUCT_ID"=> $productID
                ),
                false,
                false,
                array()
            );
            while($arDbItem = $dbBasketItems->GetNext())
            {
                $category = "CASH";
                // определение категории корзины, к которой относится товар
                $arFilter = array(
                    "BASKET_ID" => $arDbItem["ID"],
                    "@CODE"		=> $arSections
                );

                $rsSectionProp = CSaleBasket::GetPropsList(array("SORT" => "ASC"), $arFilter);
                if ($arSectionProp = $rsSectionProp->Fetch())
                    $category = $arSectionProp["CODE"];

                $bResult = CSaleBasket::Delete($arDbItem["ID"]);
                $APPLICATION->IncludeComponent("ws:small.basket", "response", array(
                        "PATH_TO_ORDER" => '/personal/basket.php',
                        "BLOCK_NAME" => "",
                        "SHOW_FIELDS" => array("NAME", "PREVIEW_PICTURE"),
                        "SECTION" => $category
                    ),
                    false
                );
                return;
            }

            $arResponse = array(
                "ERROR" => "PRODUCT_NOT_FOUND_IN_BASKET",
                "QUANTITY" => "0"
            );
        }
    }

    if(count($arResponse) == 0 && !$bResult)
    {
        $arResponse = array(
            "ERROR" => "UNKNOWN_ERROR",
            "QUANTITY" => "0"
        );
    }

    if($bResult)
    {
        $APPLICATION->IncludeComponent("ws:small.basket", "response", array(
                "PATH_TO_ORDER" => '/personal/basket.php',
                "BLOCK_NAME" => "",
                "SHOW_FIELDS" => array("NAME", "PREVIEW_PICTURE"),
                "SECTION" => $category
            ),
            false
        );
        die();
    }

    $APPLICATION->RestartBuffer();
    echo json_encode($arResponse);
    return;
}
?>