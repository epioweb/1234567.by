<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?> <?$APPLICATION->IncludeComponent("bitrix:sale.personal.order", "", array(
	
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
<?
//$res = CSaleOrder::GetByID(1009);
//printAdmin($component);
?>


 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>