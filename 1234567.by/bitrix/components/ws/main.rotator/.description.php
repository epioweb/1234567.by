<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//описание компонента
//DESC_НАЗВАНИЕ_СВОЙСТВА_ОПИСАНИЯ
$arComponentDescription = array(
    "NAME" => GetMessage("DESC_NAME_MAIN_ROTATOR"),
    "DESCRIPTION" => GetMessage("DESC_DESCRIPTION_MAIN_ROTATOR"),
    "ICON" => "/images/icon.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 30,
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