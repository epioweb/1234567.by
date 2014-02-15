<?
class CImportPrice {
    function __construct() {
        $this->pathToFile = $_SERVER["DOCUMENT_ROOT"]."/parser/docs/price.csv";
        $this->iblockID = "6";

        CModule::IncludeModule("iblock");
    }

    function Start() {
        $rsCsv = new CReadCsv();
        $arRows = $rsCsv->Read($this->pathToFile);
        foreach($arRows as $row) {
            if(strlen($row["3"]) > 0) {
                $this->SaveRow($row);
            }
        }
    }

    private function SaveRow($arRow) {
        if(strlen($arRow[0]) > 0 && strlen($arRow[3]) > 0) {
            $arFilter = array(
                "IBLOCK_ID" => $this->iblockID,
                "NAME" => $arRow[0]
            );

            $arRow["3"] = str_replace("http://1234567.by", "", $arRow["3"]);
            $rsElement = CIBlockElement::GetList(false, $arFilter, false, array("nTopCount" => 1), array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
            if($arElement = $rsElement->GetNext()) {
                if(!isset($arSections[$arElement["IBLOCK_SECTION_ID"]])) {
                    // получить путь к элементу
                    $arFilter = array(
                        "ID" => $arElement["IBLOCK_SECTION_ID"],
                        "IBLOCK_ID" => $this->iblockID
                    );
                    $rsSection = CIBlockSection::GetList(false, $arFilter, false, array("ID", "IBLOCK_ID", "UF_OLD_URL"));
                    if($arSection = $rsSection->GetNext()) {
                        $arSections[$arSection["ID"]] = $arSection["UF_OLD_URL"];
                    }
                }
                $arRow["3"] = $arSections[$arElement["IBLOCK_SECTION_ID"]].$arRow[3];
                $arRow["3"] = str_replace("//", "/", $arRow["3"]);


                CIBlockElement::SetPropertyValuesEx($arElement["ID"], $arElement["IBLOCK_ID"], array("OLD_URL_PRICE" => $arRow[3]));
            }
        }
    }
}