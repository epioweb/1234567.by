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
    if($arParams["IBLOCK_ID"] && CModule::IncludeModule("iblock"))
    {
    	$curPath = $APPLICATION->GetCurDir();
    	$fileAbs = $_SERVER["DOCUMENT_ROOT"].$curPath."1c_export.xml";
    	
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

        if($arParams["LAST_ID"] > 0) {
            $arFilter[">ID"] = $arParams["LAST_ID"];
            $fileMode = "a+";
        } else {
            $fileMode = "w";
            echo "<"."?xml version=\"1.0\" encoding=\"".LANG_CHARSET."\"?".">\n";
            echo "	<".GetMessage("PRODUCTS").">\n";
        }

        $rsElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, array("nTopCount" => $arParams["LIMIT"]), array("XML_ID","NAME", "CODE", "DETAIL_PAGE_URL", "IBLOCK_ID", "DETAIL_PICTURE"));
        $arResult["TOTAL"] = $rsElement->SelectedRowsCount();
        while($arElement = $rsElement->GetNext())
    		{
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
    			
    			echo "		<".GetMessage("PRODUCT").">\n";
    			echo "			<".GetMessage("XML_ID").">".htmlspecialchars(trim($arElement["XML_ID"]))."</".GetMessage("XML_ID").">\n";
    			echo "			<".GetMessage("URL").">".$arElement["DETAIL_PAGE_URL"]."</".GetMessage("URL").">\n";
                
                if(is_array($arElement["PREVIEW_PICTURE"]))
                    echo "          <".GetMessage("ICON").">"."http://".$_SERVER["SERVER_NAME"].$arElement["PREVIEW_PICTURE"]["SRC"]."</".GetMessage("ICON").">\n"; 
                if(is_array($arElement["DETAIL_PICTURE"]))
                    echo "          <".GetMessage("PICTURE").">"."http://".$_SERVER["SERVER_NAME"].$arElement["DETAIL_PICTURE"]["SRC"]."</".GetMessage("PICTURE").">\n"; 
    			
    			echo "		</".GetMessage("PRODUCT").">\n";
                $lastID = $arElement["ID"];
    		}

        if($arResult["TOTAL"] < $arParams["LIMIT"]) {
    	    echo "	</".GetMessage("PRODUCTS").">\n";
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
        header("Content-Type: text/xml; charset=utf-8");
        //header('Content-Length: '.strlen($contents));
        header("Content-Disposition: attachment; filename=1c_export.xml");

        $APPLICATION->RestartBuffer();
        $fp = fopen($fileAbs, "rb");
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