<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
    Processing of received parameters
*************************************************************************/

// Основные настройки
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["SECTION_CODE"] = trim($_REQUEST["SECTION_CODE"]);
$arParams["SECTION_ID"] = (int) $_REQUEST["SECTION_ID"];

$arParams["ITEMS_COUNT"] = intval($arParams["ITEMS_COUNT"]);
if($arParams["ITEMS_COUNT"]<=0)
    $arParams["ITEMS_COUNT"] = 20;

$arParams["MAX_DEPTH"] = 4;
$arParams["MAX_BLOCK_COUNT"] = 3;
$arParams["MAX_VISIBLE_SUB_SECTIONS"] = 8;

$arParams["CACHE_TYPE"] = "Y";

/*************************************************************************
            Work with cache
*************************************************************************/
if($this->StartResultCache(false))
{
    if(!CModule::IncludeModule("iblock"))
    {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }

    /**
     *  Сохранение id кеша
     */

    $curUrl = $APPLICATION->GetCurPageParam("", array("logout", "login", "clear_cache"));

    if(CModule::IncludeModule("nscache"))
        CExCacheM::SaveCacheID($curUrl, $cache_id, "N");

    if($arParams["IBLOCK_ID"] > 0) {
        if(strlen($arParams["SECTION_CODE"]) > 0 || $arParams["SECTION_ID"]) {
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE" => "Y"
            );
            if($arParams["SECTION_ID"] > 0) {
                $arFilter["ID"] = $arParams["SECTION_ID"];
            } else
                $arFilter["CODE"] = $arParams["SECTION_CODE"];
            $rsSection = CIBlockSection::GetList(false, $arFilter, false);
            if($arSection = $rsSection->GetNext()) {
                $arResult["SECTION"] = $arSection;
                $rsPath = CIBlockSection::GetNavChain($arResult["SECTION"]["IBLOCK_ID"], $arResult["SECTION"]["ID"]);
                while($arPath = $rsPath->GetNext())
                {
                    $arResult["SECTION"]["PATH"][] = $arPath;
                }
            }
        }

        if(is_array($arResult["SECTION"]))
            $arParams["MAX_DEPTH"] += $arResult["SECTION"]["DEPTH_LEVEL"];
        else
            $arParams["MAX_DEPTH"] = 1;

        $arFilter = array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIVE"    => "Y",
            ">UF_QUANTITY" => 0,
            "<=DEPTH_LEVEL" => $arParams["MAX_DEPTH"]
        );
        if(is_array($arResult["SECTION"])) {
            $arFilter["SECTION_ID"] = $arResult["SECTION"]["ID"];
        }
        // получить список корневых разделов
        $arSelect = array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "SECTION_PAGE_URL",
            "DEPTH_LEVEL",
            "UF_QUANTITY",
            "UF_TOP_SECTION",
            "LEFT_MARGIN",
            "RIGHT_MARGIN",
            "DESCRIPTION"
        );
        $rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), $arFilter, false, $arSelect);
        $totalCnt = 0;
        while($arSection = $rsSections->GetNext()) {
            $arSection["SECTION_PAGE_URL"] = CWsCatalogTools::GetSectionUrl($arSection);
            // получаем список детей раздела
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE" => "Y",
                "SECTION_ID" => $arSection["ID"],
                ">UF_QUANTITY" => 0
                //"UF_TOP_SECTION" => 1
            );
            $arSection["CHILDS"] = array();
            $rsChildSections = CIBlockSection::GetList(array("UF_SORT" => "DESC", "NAME" => "ASC"), $arFilter, false, $arSelect);
            while($arChildSection = $rsChildSections->GetNext()) {
                $arChildSection["SECTION_PAGE_URL"] = CWsCatalogTools::GetSectionUrl($arChildSection);
                $arSection["CHILDS"][] = $arChildSection;
            }
            $totalCnt += $arSection["UF_QUANTITY"];

            $arResult["SECTIONS"][] = $arSection;
        }

        // Подсчет общего количества товаров
        if(is_array($arResult["SECTION"])) {
            $totalCnt = 0;
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE"    => "Y",
                ">UF_QUANTITY" => 0,
                "DEPTH_LEVEL" => 1
            );
            $rsSections = CIBlockSection::GetList(false, $arFilter, false, array("IBLOCK_ID", "ID", "UF_QUANTITY"));
            while($arSection = $rsSections->GetNext()) {
                $totalCnt += $arSection["UF_QUANTITY"];
            }
        }
        $arResult["PRODUCT_QUANTITY"] = $totalCnt;
    } else {
        $this->AbortResultCache();
        @define("ERROR_404", "Y");
        CHTTP::SetStatus("404 Not Found");
    }

    $this->IncludeComponentTemplate();
}

if(is_array($arResult["SECTION"]["PATH"])) {
    $pathName = array();
    foreach($arResult["SECTION"]["PATH"] as $arPath) {
        $url = $arPath["SECTION_PAGE_URL"];
        if($arPath["ID"] == $arResult["SECTION"]["ID"])
            $url = "";

        $APPLICATION->AddChainItem($arPath["NAME"], $url);
        $pathName[] = $arPath["NAME"];
    }

    // установка заголовка
    if($arParams["SET_TITLE"])
    {
        if(is_array($pathName))
            $sectionName = implode(" / ", $pathName);
        else
            $sectionName = $arResult["SECTION"]["NAME"];

        $title = $arResult["SECTION"]["NAME"];
        if(strlen($arParams["SEO_TITLE_PATTERN"]) > 0)
            $title = str_replace("#NAME#", $sectionName, $arParams["SEO_TITLE_PATTERN"]);

        $APPLICATION->SetPageProperty("title", $arResult["SECTION"]["NAME"]);
        $APPLICATION->SetTitle($title, $arTitleOptions);

        // устанавливаем мета-теги
        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $keywords = str_replace("#NAME#", $arResult["SECTION"]["NAME"], $arParams["SEO_KEYWORDS_PATTERN"]);
        if(isset($arResult["SECTION"][$arParams["META_KEYWORDS"]]))
        {
            $keywords = $arResult["SECTION"][$arParams["META_KEYWORDS"]];
            if(is_array($keywords))
                $keywords = implode(" ", $keywords);
        }

        if(strlen($keywords) > 0)
            $APPLICATION->SetPageProperty("keywords", $keywords);

        if(strlen($arParams["SEO_KEYWORDS_PATTERN"]) > 0)
            $description = str_replace("#NAME#", $arResult["SECTION"]["NAME"], $arParams["SEO_DESCRIPTION_PATTERN"]);

        if(isset($arResult["SECTION"][$arParams["META_DESCRIPTION"]]))
        {
            $description = $arResult["SECTION"][$arParams["META_DESCRIPTION"]];
            if(is_array($description))
                $description = implode(" ", $description);
        }
        if(strlen($description) > 0)
            $APPLICATION->SetPageProperty("description", $description);
    }
}
$totalQuantity = "<sup>".$arResult["PRODUCT_QUANTITY"]."</sup>";
$APPLICATION->SetPageProperty("counter", $totalQuantity);