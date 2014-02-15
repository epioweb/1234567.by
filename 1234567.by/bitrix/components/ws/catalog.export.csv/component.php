<?
set_time_limit(0);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/
global $APPLICATION;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["DETAIL_URL"] = "/catalog/#SECTION_CODE#/#ELEMENT_CODE#.html";

// Параметры изображений
$arParams["DETAIL_PICTURE_MAX_WIDTH"] = 802;
$arParams["DETAIL_PICTURE_MAX_HEIGHT"] = 600;

$arParams["PREVIEW_PICTURE_MAX_WIDTH"] = 150;
$arParams["PREVIEW_PICTURE_MAX_HEIGHT"] = 150;

if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$arParams["MODE"] = trim($_GET["mode"]);
$arParams["IBLOCK_ID"] = (int) $arParams["IBLOCK_ID"];
$arParams["SECTION_ID"] = trim($_GET["id"]);
$arParams["PRICE_CODE"] = array(0 => "Розничная");
$arParams["LAST_ID"] = (int) $_REQUEST["LAST_ID"];
$arParams["LIMIT"] = 200;

$bUSER_HAVE_ACCESS = false;
if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas()
		&& !isset($_GET["mode"])
		&& is_object($GLOBALS["USER"])
		&& $GLOBALS["USER"]->IsAdmin();

if(!$bDesignMode)
{
	if(!isset($_GET["mode"]))
		return;
	$APPLICATION->RestartBuffer();
	header("Pragma: no-cache");
}
ob_start();

if(strlen($arParams["MODE"]) > 0)
{
    echo "Наименование;Цена;Категория;Гарантия;Описание краткое;Описание полное;Код;Ссылка на сайт;Ссылка на фото\n";
    if($arParams["IBLOCK_ID"] && CModule::IncludeModule("iblock"))
    {
    	$curPath = $APPLICATION->GetCurDir();
    	$fileAbs = $_SERVER["DOCUMENT_ROOT"].$curPath."1c_export.csv";
    	
    	$arFilter = array(
    		"IBLOCK_ID" => $arParams["IBLOCK_ID"]
    	);
    	
    	if(file_exists($fileAbs) && $arParams["MODE"] == "new")
    	{
    		$lastChanged = ConvertTimeStamp(filectime($fileAbs), "FULL");
    		$arFilter[">DATE_CREATE"] = $lastChanged;
    	}
        
        if($arParams["MODE"] == "category" && strlen($arParams["SECTION_ID"]) > 0)
        {
            $rsSection = CIBlockSection::GetList(array("ID"=>"ASC"), array("XML_ID"=>$arParams["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
            if($arSection = $rsSection->Fetch()) {
                $arFilter["SECTION_ID"] = $arSection["ID"];
                $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
            }
            
            if(intval($arFilter["SECTION_ID"]) == 0)
              die("Не найдена категория");
        }

        $arSelect = array(
            "ID", "XML_ID", "NAME", "CODE", "DETAIL_PAGE_URL", "IBLOCK_ID", "DETAIL_PICTURE", "DETAIL_TEXT",
            "PREVIEW_TEXT", "PROPERTY_CML2_ARTICLE", "CATALOG_PRICE_3", "IBLOCK_SECTION_ID"
        );
        $arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
        foreach($arResultPrices as $key => $value)
        {
            $arSelect[] = $value["SELECT"];
            //$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
        }
        if($arParams["LAST_ID"]) {
            $arFilter[">ID"] = $arParams["LAST_ID"];
            $fileMode = "a+";
        } else
            $fileMode = "w";

        $rsElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount" => $arParams["LIMIT"]), $arSelect);
        $arSections = array();
        $arResult["TOTAL"] = $rsElement->SelectedRowsCount();

        while($arElement = $rsElement->GetNext()) {
            if(!isset($arSections[$arElement["IBLOCK_SECTION_ID"]]) && intval($arElement["IBLOCK_SECTION_ID"]) > 0) {
                $rsSection = CIBlockSection::GetList(false, array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $arElement["IBLOCK_SECTION_ID"]), false, array("ID", "NAME"));
                if($arSection = $rsSection->GetNext()) {
                    $arSections[$arSection["ID"]] = $arSection["NAME"];
                }
            }

            if(intval($arElement["DETAIL_PICTURE"]) > 0)
            {
                $arElement["DETAIL_PICTURE"] = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
                if(is_array($arElement["DETAIL_PICTURE"]))
                {
                    $arImageParams = array(
                        "WIDTH" => $arParams["PREVIEW_PICTURE_MAX_WIDTH"],
                        "HEIGHT"    => $arParams["PREVIEW_PICTURE_MAX_WIDTH"],
                        "SRC"   => CWsImageTools::Resizer("/thumb/".$arParams["PREVIEW_PICTURE_MAX_WIDTH"]."x".$arParams["PREVIEW_PICTURE_MAX_WIDTH"]."xtrim".$arElement["DETAIL_PICTURE"]["SRC"], true)
                    );
                    $arElement["PREVIEW_PICTURE"] = $arImageParams;

                    $arImageParams = array(
                        "WIDTH" => $arParams["DETAIL_PICTURE_MAX_WIDTH"],
                        "HEIGHT"    => $arParams["DETAIL_PICTURE_MAX_WIDTH"],
                        "SRC"   => CWsImageTools::Resizer("/thumb/".$arParams["DETAIL_PICTURE_MAX_WIDTH"]."x".$arParams["DETAIL_PICTURE_MAX_WIDTH"]."xtrim".$arElement["DETAIL_PICTURE"]["SRC"], true)
                    );
                    $arElement["DETAIL_PICTURE"] = $arImageParams;
                }
            }

            $arElement["DETAIL_PAGE_URL"] = "http://".$_SERVER["SERVER_NAME"].$arElement["DETAIL_PAGE_URL"];
            echo "\"".$arElement["NAME"]."\";";
            echo "\"".round($arElement["CATALOG_PRICE_3"])."\";";
            echo "\"".$arSections[$arElement["IBLOCK_SECTION_ID"]]."\";";
            echo "\"\";";
            echo "\"".str_replace("\"", "", $arElement["PREVIEW_TEXT"])."\";";
            echo "\"".str_replace("\"", "", $arElement["DETAIL_TEXT"])."\";";
            echo "\"".$arElement["PROPERTY_CML2_ARTICLE_VALUE"]."\";";
            echo "\"".$arElement["DETAIL_PAGE_URL"]."\";";

            //if(is_array($arElement["PREVIEW_PICTURE"]))
            //    echo "\"http://".$_SERVER["SERVER_NAME"].$arElement["PREVIEW_PICTURE"]["SRC"]."\";";
            if(is_array($arElement["DETAIL_PICTURE"]))
                echo "\"http://".$_SERVER["SERVER_NAME"].$arElement["DETAIL_PICTURE"]["SRC"]."\";";
            echo "\n";
            $lastID = $arElement["ID"];
        }

        $contents = ob_get_contents();
        ob_end_clean();

    	$fp = fopen($fileAbs, $fileMode);
    	fwrite($fp, $contents);
    	fclose($fp);
    }
}

if(!$bDesignMode)
{
    if($arResult["TOTAL"] < $arParams["LIMIT"]) {
        header('Pragma: public');
        header('Cache-control: private');
        header('Accept-Ranges: bytes');
        header("Content-Type: text/csv; charset=utf-8");
        //header('Content-Length: '.strlen($contents));
        header("Content-Disposition: attachment; filename=1c_export.csv");

        $fp = fopen($fileAbs, "r");
        $contents = fread($fp, filesize($fileAbs));
        echo $contents;
        die();
    } elseif($lastID > 0) {
        $curPath = $APPLICATION->GetCurPageParam("LAST_ID=".$lastID, array("LAST_ID"));
        ?>
        <script>
            location.href = "<?=$curPath?>";
        </script>
    <?
    }
}
else
{
	$this->IncludeComponentLang(".parameters.php");

	if(
		(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
		&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
		&& !defined("BX_SESSION_ID_CHANGE")
	)
		ShowError(GetMessage("CC_BSC1_ERROR_SESSION_ID_CHANGE"));
	?><table class="data-table">
		<tr><td><?echo GetMessage("IBLOCK_TYPE")?></td><td><?echo $arParams["IBLOCK_TYPE"]?></td></tr>
	</table>
	<?
}
?>