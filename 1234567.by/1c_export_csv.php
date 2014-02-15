<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("1С-Битрикс: Управление сайтом");
?> <?$APPLICATION->IncludeComponent("ws:catalog.export.csv", ".default", array(
        "IBLOCK_TYPE" => "1c_catalog",
        "IBLOCK_ID" => 6
    ),
    false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>