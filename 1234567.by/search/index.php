<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
?>

<?
	// сортировка
	$sort_value="RANK";// значение по умолчанию
	if(strlen($_REQUEST["f"]) > 0)
	$sort_value=$_REQUEST["f"];
	
	$order_value="asc";// значение по умолчанию
	if(strlen($_REQUEST["o"]) > 0)
	$order_value=$_REQUEST["o"];

	// количество элементов на странице
	$page_result=100;
	if($_REQUEST['show']) $page_result=$_REQUEST['show'];
	
	if($_REQUEST["in_stock"]) $stock=$_REQUEST["in_stock"];
	else $stock="Y";
	
	/* указать в параметрах компонента
	"ELEMENT_SORT_ORDER" => $order_value,
	"ELEMENT_SORT_FIELD" => $sort_value,
	
	"ELEMENT_SORT_ARRAY_FIELDS" => array(
		"shows" => "SHOWS",
		"title" => "TITLE",
		"price" => "PRICE",
		"rank" => "RANK"
	),
	"SHOW_NUMBERS" => array(
		0 => 20,
		1 => 40,
		2 => 60,
		3 => 80,
		4 => 100,
	),
	"IN_STOCK" => "N"
	*/
	
?>
<?$APPLICATION->IncludeComponent("bitrix:search.page", "search_page", array(
	"RESTART" => "N",
	"NO_WORD_LOGIC" => "N",
	"CHECK_DATES" => "Y",
	"USE_TITLE_RANK" => "Y",
	"DEFAULT_SORT" => "rank",
	"FILTER_NAME" => "",
	"arrFILTER" => array(
		0 => "iblock_1c_catalog",
	),
	"arrFILTER_iblock_1c_catalog" => array(
		0 => "all",
	),
	"SHOW_WHERE" => "Y",
	"arrWHERE" => array(
	),
	"SHOW_WHEN" => "Y",
	"PAGE_RESULT_COUNT" => $page_result,
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"DISPLAY_TOP_PAGER" => "Y",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Результаты поиска",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"USE_LANGUAGE_GUESS" => "Y",
	"AJAX_OPTION_ADDITIONAL" => "",
	
	"ELEMENT_SORT_ORDER" => $order_value,
	"ELEMENT_SORT_FIELD" => $sort_value,
	
	"ELEMENT_SORT_ARRAY_FIELDS" => array(
		"shows" => "SHOWS",
		"title" => "TITLE",
		"price" => "PRICE",
		"rank" => "RANK"
	),
	"SHOW_NUMBERS" => array(
		0 => 20,
		1 => 40,
		2 => 60,
		3 => 80,
		4 => 100,
	),
	"IN_STOCK" => $stock
	),
	false
);?> 

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>