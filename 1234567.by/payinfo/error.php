<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");?><!-- wsb_order_num=44&wsb_tid=385413297 -->
	<div style="width:533px;color:red;">
		Ошибка при оплате счета <?=$_REQUEST['wsb_order_num']?>
	</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>