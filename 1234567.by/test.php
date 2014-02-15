<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
?>
<?CModule::IncludeModule("iblock");
$arFilter = array(
    "IBLOCK_ID" => 6
);
$rsSections = CIBlockSection::GetList(false, $arFilter, false, array("ID", "CODE", "IBLOCK_ID", "NAME"));
while($arDBSection = $rsSections->GetNext()) {
    $sourceCode = $arDBSection["CODE"];

    if(strlen($arDBSection["CODE"]) <= 0)
        $arDBSection["CODE"] = CUtil::translit($arDBSection["NAME"], LANGUAGE_ID, array("change_case" => "L", "replace_space" => "-", "replace_other" => "-", ""));

    $newCode = $arDBSection["CODE"];
    $rsCodeExists = CIBlockSection::GetList(array(), array("IBLOCK_ID" => 6, "CODE" => $arDBSection["CODE"]."%"), false, array("ID", "CODE"));
    $foundSectionsCnt = $rsCodeExists->SelectedRowsCount();
    if($foundSectionsCnt > 0) {
        $arCodes = array();
        $foundSectionCode = false;
        while($arCodeSection = $rsCodeExists->GetNext()) {
            if($arCodeSection["ID"] == $arDBSection["ID"]) {
                if(!isset($arCodes[$arCodeSection["CODE"]])) {
                    $newCode = $arCodeSection["CODE"];
                    $foundSectionCode = true;
                }
            }
            else {
                if($arCodeSection["CODE"] == $newCode)
                    $foundSectionCode = false;

                $arCodes[$arCodeSection["CODE"]] = $arCodeSection["CODE"];
            }
        }

        if(!$foundSectionCode) {
            $i = 1;
            while(array_key_exists($newCode."_".$i, $arCodes))
                $i++;

            $newCode .= "_".$i;
        }
    }

    if($sourceCode != $newCode) {
        $bs = new CIBlockSection();
        $bs->Update($arDBSection["ID"], array("CODE" => $newCode));
    }

}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>