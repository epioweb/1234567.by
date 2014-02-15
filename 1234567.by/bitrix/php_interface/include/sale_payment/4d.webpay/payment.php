<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));?>

<?
	//printAdmin($GLOBALS);

$wsb_storeid =  htmlspecialchars(CSalePaySystemAction::GetParamValue("SHOP_ACCOUNTID"));
$wsb_order_num = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$wsb_test = '';
if(CSalePaySystemAction::GetParamValue("SHOP_TEST") == '1')
	$wsb_test = '1';
$wsb_currency_id = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$wsb_total = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$SecretKey = htmlspecialchars(CSalePaySystemAction::GetParamValue("SecretKey"));//''
$wsb_seed = ''.(rand().rand().rand().rand());
$ts = $wsb_seed.$wsb_storeid.$wsb_order_num.$wsb_test.$wsb_currency_id.$wsb_total.$SecretKey;
$wsb_signature = sha1($ts);

global $USER;
$wsb_email = $USER->GetParam("EMAIL");


?>

<div class="tablebodytext">
<?if(CSalePaySystemAction::GetParamValue("SHOP_TEST") == '1'):?>
	<form ACTION="https://secure.sandbox.webpay.by:8843" METHOD="POST" name="formwebpay">
	<input NAME="wsb_test"  type="hidden" value="1">
<?else:?>
	<form ACTION="https://secure.webpay.by" METHOD="POST" name="formwebpay">
<?endif;?>
<input name='*scart'  type="hidden">
<input name="wsb_storeid" type="hidden" value="<?=$wsb_storeid?>">
<input name="wsb_store"  type="hidden" value="<?=htmlspecialchars(CSalePaySystemAction::GetParamValue("SHOP_ACCOUNT"))?>" >
<input NAME="wsb_order_num"  type="hidden" value="<?=$wsb_order_num?>">
<input NAME="wsb_total"  type="hidden" value="<?=$wsb_total?>">
<input NAME="wsb_currency_id"  type="hidden" value="<?=$wsb_currency_id?>" >
<input NAME="wsb_version"  type="hidden" value="2">

<input NAME="wsb_language_id"  type="hidden" value="russian">
<input NAME="wsb_seed"  type="hidden" value="<?=$wsb_seed?>">	
<input NAME="wsb_signature"  type="hidden" value="<?=$wsb_signature?>">

<input NAME="wsb_return_url"  type="hidden" value="<?='http://'.SITE_SERVER_NAME.CSalePaySystemAction::GetParamValue('wsb_return_url')?>">
<input NAME="wsb_cancel_return_url"  type="hidden" value="<?='http://'.SITE_SERVER_NAME.CSalePaySystemAction::GetParamValue('wsb_cancel_return_url')?>">

<input NAME="wsb_email"  type="hidden" value="<?=$wsb_email?>">	
<?
$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
    array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
    array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => $wsb_order_num
        ),
    false,
    false,
    array("ID",
          "CALLBACK_FUNC", 
          "MODULE", 
          "PRODUCT_ID", 
          "QUANTITY", 
          "DELAY", 
          "CAN_BUY", 
          "PRICE", 
          "WEIGHT")
    );

while ($arItems = $dbBasketItems->Fetch())
{
    if (strlen($arItems["CALLBACK_FUNC"]) > 0)
    {
        CSaleBasket::UpdatePrice($arItems["ID"], 
                                 $arItems["CALLBACK_FUNC"], 
                                 $arItems["MODULE"], 
                                 $arItems["PRODUCT_ID"], 
                                 $arItems["QUANTITY"]);
        $arItems = CSaleBasket::GetByID($arItems["ID"]);
    }
	?>
	<input type="hidden" name="wsb_invoice_item_name[]" value="<?=$arItems['NAME']?>">
	<input type="hidden" name="wsb_invoice_item_quantity[]" value="<?=$arItems['QUANTITY']?>">
	<input type="hidden" name="wsb_invoice_item_price[]" value="<?=$arItems['PRICE']?>">
	<?
    $arBasketItems[] = $arItems;
}
if ($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]['PRICE_DELIVERY'] > 0 ){
	 $wsb_shipping_name = GetMessage("PS8");
$arDeliv = CSaleDelivery::GetByID($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]['DELIVERY_ID']);
if ($arDeliv)
{
   $wsb_shipping_name = $arDeliv["NAME"];
}

?>	
<input type="hidden" name="wsb_shipping_name" value="<?=$wsb_shipping_name?>">
<input type="hidden" name="wsb_shipping_price" value="<?=$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]['PRICE_DELIVERY']?>">
<?}?>	
	
<?=GetMessage("PS1")?> <b>Webpay</b>.<br><br>
<?=GetMessage("PS2")?> <?= $wsb_order_num.GetMessage("PS7").htmlspecialcharsEx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]) ?><br>
<?=GetMessage("PS3")?> <b><?echo SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]) ?></b><br>
<br>
<br>
<?=GetMessage("PS4")?><br>
<textarea rows="5" name="OrderDetails" cols="60">
<?=GetMessage("PS5")?> <?= $wsb_order_num.GetMessage("PS7").htmlspecialchars($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]) ?>
</textarea><br>
<br>
<input type="Submit" name="Ok" value="<?=GetMessage("PS6")?>">
</form>
</div>
<script>
	document.forms["formwebpay"].submit();
</script>


