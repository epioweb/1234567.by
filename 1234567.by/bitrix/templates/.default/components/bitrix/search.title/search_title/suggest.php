<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent("ws:search.page", "response", array(
    "IBLOCK_ID" => 6,
    "PRICE_CODE" => 3
    ),
    false
);?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>