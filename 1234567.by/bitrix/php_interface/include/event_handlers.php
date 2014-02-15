<?

AddEventHandler("catalog", "OnStoreProductUpdate", array("My_Class", "OnStoreProductUpdateHandler"));

class My_Class {

    function OnStoreProductUpdateHandler($ID, $arFields) {
        $dbStore = CCatalogStore::GetList(array('ID' => 'DESC'), array('ACTIVE' => 'Y'), false, false, array('ID'));
        while ($arStore = $dbStore->GetNext()) {
            $timeAR[] = $arStore;
        }
        if ($arFields['STORE_ID'] == $timeAR[0]['ID']) {
            $AMOUNT = 0;
            $dbRes = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arFields['PRODUCT_ID']), false, false, array());
            while ($arRes = $dbRes->GetNext()) {
                $AMOUNT+=$arRes['AMOUNT'];
            }
            CIBlockElement::SetPropertyValues($arFields['PRODUCT_ID'], IBLOCK_CATALOG_ID, (int) $AMOUNT, 'TOTAL_QUANTITY');
            if (CModule::IncludeModule('iblock')) {
                $arOder = array('ID' => 'ASC');
                $arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ID' => $arFields['PRODUCT_ID']);
                $arSelect = array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PROPERTY_SUBSCRIBERS', 'PROPERTY_TOTAL_QUANTITY');
                $dbElem = CIBlockElement::GetList($arOder, $arFilter, false, false, $arSelect);
                $arElem = $dbElem->GetNext();
                if (!empty($arElem['PROPERTY_SUBSCRIBERS_VALUE']) and $AMOUNT > 0) {
                    $arProp = array(
                        'PRODUCT_ID' => $arElem['ID'],
                        'PRODUCT_NAME' => $arElem['NAME'],
                        'PRODUCT_QUANTITY' => $AMOUNT,
                        'EMAIL_TO' => implode(', ', $arElem['PROPERTY_SUBSCRIBERS_VALUE']),
                        'PRODUCT_PAGE' => 'http://1234567.by' . '' . $arElem['DETAIL_PAGE_URL'],
                    );
                    CEvent::Send('SUBSCRIBE_SEND', 's1', $arProp);
                    CIBlockElement::SetPropertyValues($arFields['PRODUCT_ID'], IBLOCK_CATALOG_ID, array(), "SUBSCRIBERS");
                }
            }
        }
        /*
          $AMOUNT=0;
          $dbRes=CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$arFields['PRODUCT_ID']), false, false, array());
          while($arRes=$dbRes->GetNext()){
          $AMOUNT+=$arRes['AMOUNT'];
          }
          CIBlockElement::SetPropertyValues($arFields['PRODUCT_ID'], IBLOCK_CATALOG_ID, (int)$AMOUNT, 'TOTAL_QUANTITY');
          if(CModule::IncludeModule('iblock')){
          $arOder=array('ID'=>'ASC');
          $arFilter=array('IBLOCK_ID'=>IBLOCK_CATALOG_ID, 'ID'=>$arFields['PRODUCT_ID']);
          $arSelect=array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PROPERTY_SUBSCRIBERS');
          $dbElem=CIBlockElement::GetList($arOder, $arFilter, false, false, $arSelect);
          if($arElem=$dbElem->GetNext()){
          echo "<pre>";
          echo var_dump($arElem);
          echo "</pre>";
          die();
          }
          }
         */
    }

}

//Set propertie STRING_FOR_CHECKING in ORDER
AddEventHandler("sale", "OnSaleComponentOrderOneStepDiscountBefore", "addStringForChecking");
   function addStringForChecking(&$arResult,&$arUserResult,$arParams) {
      $arUserResult["ORDER_PROP"][6] = randString(30, array(
         "abcdefghijklnmopqrstuvwxyz",
         "ABCDEFGHIJKLNMOPQRSTUVWX­YZ",
         "0123456789"         
      ));
}

//Отправка письма при смене статуса заказа на "W"(В ожидании оплаты)
AddEventHandler("sale", "OnSaleStatusOrder", Array("mail", "OnSaleStatusOrder_mail"));

class mail {
    function OnSaleStatusOrder_mail($ID, $val) {

         // Код статуса заказа, при котором отправлять письмо (W)
        if ($val == "W") {
            // Получаем параметры заказа
            $arOrder = CSaleOrder::GetByID($ID);
            $text = "Для просмотра подробной информации о заказе перейдите по ссылке: http://alo-alo.ru/personal/order/";
            //Получаем свойства заказа
            $db_props = CSaleOrderPropsValue::GetOrderProps($ID);

            // Получаем код статуса заказа
            $arStatus = CSaleStatus::GetByID($val);
            $arStatus_opis = $arStatus["DESCRIPTION"];
            $arStatus = $arStatus["NAME"];

            $EMAIL = "";
            while ($arProps = $db_props->Fetch()) {
               $props[$arProps["CODE"]]=$arProps["VALUE"];
               if ($arProps["CODE"] == "EMAIL") {
                  $EMAIL = $arProps["VALUE"];
               }
            }

            $arEventFields = array(
                "ORDER_ID" => $ID,
                "ORDER_STATUS" => $arStatus,
                "ORDER_DATE" => $arOrder["DATE_INSERT"],
                "EMAIL" => $EMAIL,
                "ORDER_DESCRIPTION" => $arStatus_opis,
                "SALE_EMAIL" => "epioweb@mail.com",
                "TEXT" => $text,
                "STRING_FOR_CHECKING" => $props["STRING_FOR_CHECKING"]
            );
            AddMessage2Log($props,false);
            CEvent::SendImmediate("SALE_STATUS_CHANGED_N", s1, $arEventFields, "N", 71);
        }
    }

}

?>