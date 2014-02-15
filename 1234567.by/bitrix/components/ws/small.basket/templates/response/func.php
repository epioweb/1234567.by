<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$APPLICATION->IncludeComponent("newsite:basket", ".default", array(
	"PATH_TO_ORDER" => $_REQUEST["PATH_TO_ORDER"],
	"BLOCK_NAME" => "",
	"IBLOCK_TYPE" => $_REQUEST["IBLOCK_TYPE"],
	"IBLOCK_ID" => $_REQUEST["IBLOCK_ID"],
	"SHOW_FIELDS" => array(),
	"LINK_FIELD" => $_REQUEST["LINK_FIELD"]
	),
	false
);?>  

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>