<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?
	/*if(Cmodule::IncludeModule('catalog')){
		$myCatalog = new CCatalogProduct;
		$arOrder=array('ID'=>'ASC');
		$arFilter=array('QUANTITY' => '0');
		$arSelect=array('ID','QUANTITY','ELEMENT_NAME');
		
		$db_res=$myCatalog::GetList($arOrder, $arFilter, false, false, $arSelect);
		while($ar_res=$db_res->GetNext()){
			echo '<pre>';
			var_dump($ar_res);
			echo '</pre>';
		}
	}
	if(CModule::IncludeModule('iblock')){
		$arOrder=array('ID' => 'ASC');
		$arFilter=array('IBLOCK_ID' => '6');
		$arSelect=array("ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_TOTAL_QUANTITY");
	
		$db_res=CIblockElement::GetList(array(), $arFilter, false, false, $arSelect);
		while($ar_res=$db_res->GetNext()){
			//$arProps = $ar_res->GetProperties();
			echo '<pre>';
			var_dump($ar_res);
			echo '</pre>';
		}
	}*/
	if(CModule::IncludeModule('catalog')){
		$arOrderCat=array('ID'=>'ASC');
		$IBLOCK_ID=6;
		$arFilterCat=array();
		$arSelectCat=array('ID', 'QUANTITY');
		$db_cat_res=CCatalogProduct::GetList($arOrderCat, $arFilterCat, false, false, $arSelectCat);
		while($ar_cat_res=$db_cat_res->GetNext()){
			//CIBlockElement::SetPropertyValues($ar_cat_res['ID'], $IBLOCK_ID, (int)$ar_cat_res['QUANTITY'], 'TOTAL_QUANTITY');
		}
	}
	/*
	if(CModule::IncludeModule('iblock')){
		$ELEMENT_ID = 16940;  // код элемента
		$X=9;
		$PROPERTY_CODE = "TEST";  // код свойства
		$PROPERTY_VALUE = "ZZZZZZZZZ";  // значение свойства
		if(CIBlockElement::SetPropertyValues($ELEMENT_ID, $X, $PROPERTY_VALUE,$PROPERTY_CODE)) echo "OK";
		else echo 'NO';
	
	}*/
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>