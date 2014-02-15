<?
class CProductList {
    private $url;
    function __construct($url, $pageNumber = 0) {
        $this->url = $url;
        $this->pageNum = $pageNumber;
        $this->iblockID = 6;
    }

    public function GetList() {
        CLog::Log("Получение списка товаров - ".$this->url, 3);

        $remote = new CRemote($this->url);
        $contents = $remote->GetContents();
        $arSections = $this->ParseContent($contents);

        return $arSections;
    }
    private function ParseContent($contents) {
        $contentPattern = "/<div class=\"sortRazbItems\">(.*?)<div class=\"sortRazbItems\">/si";
        preg_match($contentPattern, $contents, $matchesContent);

        if(count($matchesContent) > 0) {
            $pattern = "/<a class=\"ja\-newstitle\" href=\"(.*?)\".*?>(.*?)<\/a>/si";
            preg_match_all($pattern, $matchesContent[0], $matches);

            if(is_array($matches[1])) {
                $arProducts = array();
                foreach($matches[1] as $key=>$v) {
                    $arProducts[$key] = array(
                        "NAME" => CParserUtil::StrIconv($matches[2][$key]),
                        "URL"   => $v
                    );
                }

                // постраничная навигация
                $pagingPattern = "/<ul class=\"page_div\">(.*?)<\/ul>/si";
                preg_match($pagingPattern, $matchesContent[0], $pagingUrls);
                if(strlen($pagingUrls[1]) > 0) {
                    // страницы
                    $pagingItemsPattern = "/<a href=\"(.*?)\">(\d+)<\/a>/si";
                    preg_match_all($pagingItemsPattern, $pagingUrls[1], $pagingItemsUrls);
                    if(count($pagingItemsUrls[1]) > 0) {
                        foreach($pagingItemsUrls[1] as $index => $url) {
                            if($this->pageNum == 0) {
                                $serverUrl = $this->url;
                                if(stripos($this->url, "?") !== false) {
                                    $arServerUrl = explode("?", $this->url);
                                    $serverUrl = $arServerUrl[0];
                                }

                                $url = html_entity_decode($url);
                                CLog::Log($serverUrl.$url, 5);
                                $rsProduct = new CProductList($serverUrl.$url, $pagingItemsUrls[2][$index]);
                                $pageProducts = $rsProduct->GetList();
                                if(count($pageProducts) > 0) {
                                    foreach($pageProducts as $arProduct) {
                                        $arProducts[] = $arProduct;
                                    }
                                } else {
                                    CLog::Log("Постраничная навигация. Товары не найдены. ".$serverUrl, 4);
                                }
                            }
                        }
                    }
                }
                return $arProducts;
            }
        }

        return false;
    }

    public function SaveUrl($arProduct) {
        CLog::Log("Сохранение товара ".$arProduct["NAME"], 4);
        if(CModule::IncludeModule("iblock")) {

            $arFilter = array(
                "IBLOCK_ID" => $this->iblockID,
                "NAME" => $arProduct["NAME"]
            );
            $rsProduct = CIBlockElement::GetList(false, $arFilter, false, array("nTopCount" => 1), array("ID", "IBLOCK_ID"));
            if($arIblockProduct = $rsProduct->GetNext()) {
                CIBlockElement::SetPropertyValuesEx($arIblockProduct["ID"], $arIblockProduct["IBLOCK_ID"], array("OLD_URL" => $arProduct["URL"]));
            }
        }
    }
}