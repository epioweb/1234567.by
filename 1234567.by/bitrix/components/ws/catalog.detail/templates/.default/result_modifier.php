<?

$res=CIBlockElement::GetList(
 Array("SORT"=>"ASC",),
 Array("IBLOCK_ID"=> $arResult["SECTION"]["IBLOCK_ID"], "ID"=>$arResult["SECTION"]["ID"]),
 false,
 false,
 Array()
);
//printAdmin($res->GetNextElement()->GetProperties());