<?
if(!defined("MANUAL_404"))
    include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$curl = $APPLICATION->GetCurPage();
if(CModule::IncludeModule("oldurl"))
{
    $rUrl = COldUrlAll::GetRedirectUrl($curl);
    if($rUrl)
    {
        $APPLICATION->RestartBuffer();
        LocalRedirect($rUrl);
    }
}

// получаем текущий урл и пытаемся найти его в базе
if(CModule::IncludeModule("iblock"))
{
    if(strpos($curl, "/images/cache/") === false)
    {

        // ищем урл в раздела
        $obCache = new CPHPCache;
        $life_time = 86400;
        $url = "";
        // ищем урл в элементах
        $cache_id = "OLD_DETAIL_URL_".$curl;
        if($obCache->InitCache($life_time, $cache_id, "/"))
        {
            // получаем закешированные переменные
            $arData = $obCache->GetVars();
            if(isset($arData["URL"]))
                $url = $arData["URL"];
        }
        else
        {
            if($obCache->StartDataCache())
            {
                $arFilter = array(
                    "ACTIVE" => "Y",
                    //"PROPERTY_OLD_URL" => $curl,
                    "IBLOCK_ID" => "6"
                );
                $arFilter[] = array(
                    "LOGIC" => "OR",
                    array(
                        "PROPERTY_OLD_URL" => $curl
                    ),
                    array(
                        "PROPERTY_OLD_URL_PRICE" => $curl,
                    )
                );

                $rsElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount"=>1), array("ID","IBLOCK_ID","CODE", "DETAIL_PAGE_URL"));
                if($arElement = $rsElement->GetNext())
                {
                    $url = $arElement["DETAIL_PAGE_URL"];
                }

                $obCache->EndDataCache(array(
                    "URL"    => $url
                ));

            }
        }

        if(strlen($url) > 0) {
            LocalRedirect($url);
        }

        $cache_id = "OLD_SECTION_URL_".$curl;
        if($obCache->InitCache($life_time, $cache_id, "/"))
        {
            // получаем закешированные переменные
            $arData = $obCache->GetVars();
            if(isset($arData["URL"]))
                $url = $arData["URL"];
        }
        else
        {
            if($obCache->StartDataCache())
            {
                $arFilter = array(
                    "UF_OLD_URL" => $curl,
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => 6
                );

                $rsSection = CIBlockSection::GetList(array("ID"=>"ASC"), $arFilter, false, array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "SECTION_PAGE_URL"));
                if($rsSection->SelectedRowsCount() == 0) {
                    $tmpUrl = "/";
                    $arTmpUrl = explode("/", $curl);
                    foreach($arTmpUrl as $r) {
                        if(strlen($r) > 0) {
                            $tmpUrl .= $r."/";
                            $arFilter["UF_OLD_URL"] = $tmpUrl;

                            $tmpSection = CIBlockSection::GetList(array("ID"=>"ASC"), $arFilter, false, array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "SECTION_PAGE_URL"));
                            if($tmpSection->SelectedRowsCount() > 0)
                                $rsSection = $tmpSection;
                        }
                    }
                }

                if($arSection = $rsSection->GetNext())
                {
                    if($arSection["RIGHT_MARGIN"] - $arSection["LEFT_MARGIN"] == 1)
                        $arSection["SECTION_PAGE_URL"] = str_replace("/s_", "/", $arSection["SECTION_PAGE_URL"]);

                    $url = $arSection["SECTION_PAGE_URL"];
                }

                $obCache->EndDataCache(array(
                    "URL" => $url
                ));
            }
        }
        if(strlen($url) > 0)
            LocalRedirect($url);
    }
}


$APPLICATION->SetTitle("Страница не найдена");

$APPLICATION->IncludeComponent("bitrix:main.map", ".default", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"SET_TITLE" => "Y",
	"LEVEL"	=>	"3",
	"COL_NUM"	=>	"2",
	"SHOW_DESCRIPTION" => "Y"
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>