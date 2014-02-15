<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?
$arDetParams = array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
		"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ID" => $_REQUEST["order"],
	);

$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.detail",
	"",
	$arDetParams,
	$component
);
?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>