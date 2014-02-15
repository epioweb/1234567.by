<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("BCI1_NAME"),
	"DESCRIPTION" => GetMessage("BCI1_DESCRIPTION"),
	"ICON" => "/images/1c-imp.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 120,
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