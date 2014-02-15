<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");

if (CModule::IncludeModule("sale"))
{ if ( $_REQUEST['wsb_tid'] ){ ?>

	Счет <?=$_REQUEST['wsb_order_num']?> оплачен. <br/> Код транзакции <?=$_REQUEST['wsb_tid']?>
    <br/> <br/>
    Информация об оплате в скором времени будет подтвеждена администратором.  
	<br/> <br/>
	<a href="<?=SITE_DIR?>/personal">Перейти в линый кабинет</a>
<?
  if (!CSaleOrder::PayOrder($_REQUEST['wsb_order_num'], "Y", True, True, 0, array("PAY_VOUCHER_NUM" => $_REQUEST['wsb_tid'])))
  {
     echo "Ошибка обновления информации о заказе.";
  } 
  
 } else 
 	echo 'Не переданы параметры.';
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>