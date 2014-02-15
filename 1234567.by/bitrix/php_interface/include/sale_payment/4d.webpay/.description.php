<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/.description.php"));

$psTitle = GetMessage("SPCP_DTITLE");
$psDescription = GetMessage("SPCP_DDESCR");

$arPSCorrespondence = array(
		"SHOP_ACCOUNTID" => array(
				"NAME" => GetMessage("SHOP_ACCOUNTID_N"),
				"DESCR" => GetMessage("SHOP_ACCOUNTID_D"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SHOP_ACCOUNT" => array(
				"NAME" => GetMessage("SHOP_ACCOUNT_N"),
				"DESCR" => GetMessage("SHOP_ACCOUNT_D"),
				"VALUE" => "",
				"TYPE" => ""
		),
		"SecretKey" => array(
				"NAME" => GetMessage("SecretKey_N"),
				"DESCR" => GetMessage("SecretKey_D"),
				"VALUE" => "",
				"TYPE" => ""
		),
		"wsb_return_url" => array(
				"NAME" => GetMessage("wsb_return_url_N"),
				"DESCR" => GetMessage("wsb_return_url_D"),
				"VALUE" => "/payinfo/ok.php",
				"TYPE" => ""
		),
		"wsb_cancel_return_url" => array(
				"NAME" => GetMessage("wsb_cancel_return_url_N"),
				"DESCR" => GetMessage("wsb_cancel_return_url_D"),
				"VALUE" => "/payinfo/error.php",
				"TYPE" => ""
		),
		"wsb_notify_url" => array(
				"NAME" => GetMessage("wsb_notify_url_N"),
				"DESCR" => GetMessage("wsb_notify_url_D"),
				"VALUE" => "/payinfo/ok.php",
				"TYPE" => ""
		),
		"SHOP_LOGIN" => array(
				"NAME" => GetMessage("SHOP_LOGIN_N"),
				"DESCR" => GetMessage("SHOP_LOGIN_D"),
				"VALUE" => "",
				"TYPE" => ""
		),
		"SHOP_PASSWORD" => array(
				"NAME" => GetMessage("SHOP_PASSWORD_N"),
				"DESCR" => GetMessage("SHOP_PASSWORD_D"),
				"VALUE" => "",
				"TYPE" => ""
		),
		"SHOP_TEST" => array(
				"NAME" => GetMessage("SHOP_TEST_N"),
				"DESCR" => GetMessage("SHOP_TEST_D"),
				"VALUE" => "1",
				"TYPE" => ""
		)
	
	);
?>