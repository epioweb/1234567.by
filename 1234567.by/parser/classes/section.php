<?
class CSection {
    private $url;

    function __construct($url, $currenSection = "", $currentSectionUrl = "") {
        $this->url = $url;
        $this->currentSection = $currenSection;
        $this->currentSectionUrl = $currentSectionUrl;

        $this->iblockID = 6;
    }
    public function GetList() {
        CLog::Log("Получение списка разделов - ".$this->url, 1);

        $remote = new CRemote($this->url);
        $contents = $remote->GetContents();
        $arSections = $this->ParseContent($contents);

        return $arSections;
    }
    private function ParseContent($contents) {
        $menuContentPattern = "/<div id=\"ja\-col1\">(.*?)<\/div>/si";
        preg_match($menuContentPattern, $contents, $matchesContent);

        if(count($matchesContent) > 0) {
            if(strlen($this->currentSection) > 0) {
                // определяем блок с нужными ссылками
                $pattern = "/<\/h3>[\s\t\n]*?<a(.*?)<div>/si";
                preg_match($pattern, $matchesContent[1], $matchesBlock);
                if(strlen($matchesBlock[1]) > 0)
                    $matchesContent[1] = "<a".$matchesBlock[1];
            }

            $pattern = "/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>/si";
            preg_match_all($pattern, $matchesContent[1], $matches);
        }

        if(count($matches) == 0) {
            CLog::Log("Ничего не найдено",2);
        } else {

            $arSections = false;
            foreach($matches[1] as $key=>$v) {
                if(strlen($this->currentSection) > 0 && (stripos($v, $this->currentSectionUrl) === false || $matches[2][$key] == $this->currentSection))
                    continue;

                $arSections[$key] = array(
                    "NAME" => CParserUtil::StrIconv($matches[2][$key]),
                    "URL"   => $v
                );
            }

            return $arSections;
        }

        return false;
    }

    public function SaveUrl($arSection) {
        CLog::Log("Сохранение раздела ".$arSection["NAME"], 4);
        if(CModule::IncludeModule("iblock")) {

            $arFilter = array(
                "IBLOCK_ID" => $this->iblockID,
                "NAME" => $arSection["NAME"]
            );
            $rsSection = CIBlockSection::GetList(false, $arFilter, false, array("ID", "IBLOCK_ID"));
            if($arIblockSection = $rsSection->GetNext()) {
                $arFields = array(
                    "IBLOCK_ID" => $this->iblockID,
                    "UF_OLD_URL" => $arSection["URL"]
                );
                $bs = new CIBlockSection();
                $bs->Update($arIblockSection["ID"], $arFields);
            }
        }
    }
}