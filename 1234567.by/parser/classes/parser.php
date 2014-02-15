<?
class CParser {
    private $serverName;
    private $url;

    function __construct($currentSection = "", $currentSectionUrl = "") {
        $this->serverName = "http://1234567.by";
        $this->currentSection = $currentSection;
        $this->currentSectionUrl = $currentSectionUrl;
    }

    public function Start($url = "") {
        $this->url = $this->serverName.$url;
        $rsSections = new CSection($this->url, $this->currentSection, $this->currentSectionUrl);
        $arSections = $rsSections->GetList();

        if(is_array($arSections)) {
            foreach($arSections as $arSection) {
                CLog::Log("Обработка раздела ".$arSection["NAME"], 2);
                $rsSections->SaveUrl($arSection);

                // получить список товаров
                if(strlen($this->currentSection) <= 0)
                    $sectionUrl = $this->url.$arSection["URL"];
                else
                    $sectionUrl = $this->serverName.$arSection["URL"];

                $rsProduct = new CProductList($sectionUrl);
                $arProducts = $rsProduct->GetList();
                if(is_array($arProducts)) {
                    foreach($arProducts as $arProduct) {
                        if(stripos($arProduct["URL"], $arSection["URL"]) === false)
                            $arProduct["URL"] = $arSection["URL"].$arProduct["URL"];

                        $rsProduct->SaveUrl($arProduct);
                    }
                } else {
                    if($arSection["NAME"] != $this->currentSection) {
                        CLog::Log("Товары не найдены. Получение списка разделов", 3);
                        // получение подразделов
                        $subSections = new CParser($arSection["NAME"], $arSection["URL"]);
                        $subSections->Start($arSection["URL"]);
                    }
                }
            }
        }
    }
}