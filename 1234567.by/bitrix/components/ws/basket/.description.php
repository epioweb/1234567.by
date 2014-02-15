<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SBB_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("SBB_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/sale_basket.gif",
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