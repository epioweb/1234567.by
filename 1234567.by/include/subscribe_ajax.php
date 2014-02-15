<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?
	if($_REQUEST['email'] and $_REQUEST['id']){
		if(CModule::IncludeModule('iblock')){
			$arFilter=array('IBLOCK_ID'=>IBLOCK_CATALOG_ID, 'ID'=>$_REQUEST['id']);
			$arSelect=array('ID', 'PROPERTY_SUBSCRIBERS');
			$db=CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false,false,$arSelect);
			if($ar=$db->GetNext()){
			}
			$subscribers=array();
			foreach($ar['PROPERTY_SUBSCRIBERS_VALUE'] as $item){
				$subscribers[]=array("VALUE"=>$item);
			}
			$current= array("VALUE"=>$_REQUEST['email']);
			$subscribers[count($subscribers)]=$current;
			CIBlockElement::SetPropertyValues($_REQUEST['id'], IBLOCK_CATALOG_ID, $subscribers, "SUBSCRIBERS");
		}
	}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>