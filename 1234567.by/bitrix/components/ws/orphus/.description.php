<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESC"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "N",
	"SORT" => 0,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "catalog",
			"NAME" => GetMessage("T_IBLOCK_DESC_CATALOG"),
			"SORT" => 0,
			"CHILD" => array(
				"ID" => "catalog_cmpx",
			),
		),
	),
);
?>