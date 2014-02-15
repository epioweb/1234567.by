<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

?>

<?
// выберем хиты сессии #1056
$arFilter = array(
    "DATE_1" => "21.01.2013",
	"DATE_2" => "21.01.2014",
    "URL" => $APPLICATION->GetCurUri()
    );

// получим список записей
$rs = CHit::GetList(
    ($by = "s_id"), 
    ($order = "desc"), 
    $arFilter, 
    $is_filtered
    );

// выведем все записи
$i=0;
while ($ar = $rs->Fetch())
{
	//echo "<pre>"; print_r($ar); echo "</pre>";    
$i++;
}
printAdmin($i);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>