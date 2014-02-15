<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/catalog/([0-9a-z\\_\\-]+)/([0-9a-z\\_\\-]+)\\.html.*#",
		"RULE" => "SECTION_CODE=\$1&ELEMENT_CODE=\$2",
		"ID" => "",
		"PATH" => "/catalog/detail.php",
	),
	array(
		"CONDITION" => "#^/catalog/s\\_([0-9a-z\\_\\-]+)/.*#",
		"RULE" => "SECTION_CODE=\$1",
		"ID" => "",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/([0-9a-z\\_\\-]+)/.*#",
		"RULE" => "SECTION_CODE=\$1",
		"ID" => "",
		"PATH" => "/catalog/list.php",
	),
	array(
		"CONDITION" => "#^/news/([0-9a-z\\_\\-]+)/.*#",
		"RULE" => "ELEMENT_CODE=\$1",
		"ID" => "",
		"PATH" => "/news/detail.php",
	),
	array(
		"CONDITION" => "#^/personal/order/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/personal/order/index.php",
	),
	array(
		"CONDITION" => "#^/about/idea/#",
		"RULE" => "",
		"ID" => "bitrix:idea",
		"PATH" => "/about/idea/index.php",
	),
	array(
		"CONDITION" => "#^/test/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/test/index.php",
	),
);

?>