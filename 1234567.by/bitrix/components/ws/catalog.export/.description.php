<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NEWSITE_EXPORT_NAME"),
	"DESCRIPTION" => GetMessage("NEWSITE_EXPORT_DESCRIPTION"),
	"ICON" => "/images/1c-imp.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 120,
	"PATH" => array(
		"ID" => "Newsite",
		"CHILD" => array(
			"ID" => "Newsite",
			"NAME" => GetMessage("NEWSITE_BCI1_CATALOG"),
			"SORT" => 30,
		),
	),
);

?>