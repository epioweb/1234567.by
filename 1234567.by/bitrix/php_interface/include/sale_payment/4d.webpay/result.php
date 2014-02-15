<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
	<?include(GetLangFileName(dirname(__FILE__)."/", "/result.php"));?>
<?

$pRes['1'] = GetMessage('PS1');
$pRes['2'] = GetMessage('PS2');
$pRes['3'] = GetMessage('PS3');
$pRes['4'] = GetMessage('PS4');
$pRes['5'] = GetMessage('PS5');
$pRes['6'] = GetMessage('PS6');
$pRes['7'] = GetMessage('PS7');
	
$webpay_test = CSalePaySystemAction::GetParamValue("SHOP_TEST");
$LOGIN = CSalePaySystemAction::GetParamValue("SHOP_LOGIN");
$PASSWORD = CSalePaySystemAction::GetParamValue("SHOP_PASSWORD");

$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);

set_time_limit(0);

$postdata = '*API=&API_XML_REQUEST='.urlencode('
<?xml version="1.0" encoding="ISO-8859-1" ?>
<wsb_api_request>
<command>get_transaction</command>
<authorization>
<username>'.$LOGIN.'</username>
<password>'.md5($PASSWORD).'</password>
</authorization>
	<fields><transaction_id>'.$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]['PAY_VOUCHER_NUM'].'</transaction_id>
</fields>
</wsb_api_request>
');

$link = "https://billing.webpay.by";
if ( $webpay_test == '1' )
	$link = "https://sandbox.webpay.by";

$curl = curl_init($link); 
	
curl_setopt ($curl, CURLOPT_HEADER, 0);
curl_setopt ($curl, CURLOPT_POST, 1);
curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);

$sResult = curl_exec ($curl);

curl_close ($curl);

if ($sResult <> "")
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

	$objXML = new CDataXML();
	$objXML->LoadString($sResult);
	$arResult = $objXML->GetArray();
  
  //global $USER;
  
	if (count($arResult)>0 && $arResult["wsb_api_response"]["#"]["status"][0]['#'] == "success")
	{
			$fields = $arResult["wsb_api_response"]["#"]["fields"][0]['#'];
			$aSuccess = array('1','4');//array("Completed", "Authorized");
			$arFields = array(
					"PS_STATUS" => (in_array($fields["payment_type"][0]["#"], $aSuccess)?"Y":"N"),
					"PS_STATUS_CODE" => $fields["payment_type"][0]["#"],
					"PS_STATUS_DESCRIPTION" => $pRes[$fields["payment_type"][0]["#"]],
					"PS_STATUS_MESSAGE" => (GetMessage('PS8').$fields["order_id"][0]["#"].GetMessage('PS9').$fields["rrn"][0]["#"].GetMessage('PS10').$fields["transaction_id"][0]["#"]),
					"PS_SUM" => DoubleVal($fields["amount"][0]["#"]),
					"PS_CURRENCY" => $fields["currency_id"][0]["#"],
					"PS_RESPONSE_DATE" => Date( CDatabase::DateFormatToPHP( CLang::GetDateFormat( "FULL" , LANG ) ) ),
				);
			if ( CSaleOrder::Update( $ORDER_ID, $arFields) ){
				//uppendMany($GLOBALS["SALE_INPUT_PARAMS"]['USER']['LOGIN'],DoubleVal($fields["amount"][0]["#"]));
				return true;
			}
	} elseif(count($arResult)>0 && $arResult["wsb_api_response"]["#"]["status"][0]['#'] == "failed") {
					$arFields = array(
					"PS_STATUS" => "N",
					"PS_STATUS_CODE" => '0',
					"PS_STATUS_DESCRIPTION" => GetMessage('PS11'),
					"PS_STATUS_MESSAGE" => $arResult["wsb_api_response"]["#"]["error"][0]['#']["error_message"][0]['#'],
					"PS_RESPONSE_DATE" => Date( CDatabase::DateFormatToPHP( CLang::GetDateFormat( "FULL" , LANG ) ) ),
				);
		 if ( CSaleOrder::Update( $ORDER_ID, $arFields) )
			return true;
	}
}

return false;
?>
