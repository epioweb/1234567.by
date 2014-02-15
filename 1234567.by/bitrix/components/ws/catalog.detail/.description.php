<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("WS_PRODUCT_DETAIL"),
    "DESCRIPTION" => GetMessage("WS_PRODUCT_DETAIL_DESCRIPTION"),
    "COMPLEX" => "N",
    "SORT" => 0,
    "PATH" => array(
        "ID" => "ws",
        "CHILD" => array(
            "ID" => "ws.catalog",
            "NAME" => GetMessage("T_BESTPRICE"),
            "SORT" => 10,
        ),
    ),
);
?>