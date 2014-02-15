<?
define('BX_SESSION_ID_CHANGE', false);
define('BX_SKIP_POST_UNQUOTE', true);
if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "crm")
{
    define("LANG", "en");
    define("ADMIN_SECTION", true);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->IncludeComponent("ws:catalog.import.1c", "", Array(
            "IBLOCK_TYPE" => COption::GetOptionString("catalog", "1C_IBLOCK_TYPE", "-"),
            "SITE_LIST" => array(COption::GetOptionString("catalog", "1C_SITE_LIST", "-")),
            "INTERVAL" => COption::GetOptionString("catalog", "1C_INTERVAL", "-"),
            "GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS", "")),
            "GENERATE_PREVIEW" => COption::GetOptionString("catalog", "1C_GENERATE_PREVIEW", "Y"),
            "PREVIEW_WIDTH" => COption::GetOptionString("catalog", "1C_PREVIEW_WIDTH", "100"),
            "PREVIEW_HEIGHT" => COption::GetOptionString("catalog", "1C_PREVIEW_HEIGHT", "100"),
            "DETAIL_RESIZE" => COption::GetOptionString("catalog", "1C_DETAIL_RESIZE", "Y"),
            "DETAIL_WIDTH" => COption::GetOptionString("catalog", "1C_DETAIL_WIDTH", "300"),
            "DETAIL_HEIGHT" => COption::GetOptionString("catalog", "1C_DETAIL_HEIGHT", "300"),
            "ELEMENT_ACTION" => COption::GetOptionString("catalog", "1C_ELEMENT_ACTION", "D"),
            "SECTION_ACTION" => COption::GetOptionString("catalog", "1C_SECTION_ACTION", "D"),
            "FILE_SIZE_LIMIT" => COption::GetOptionString("catalog", "1C_FILE_SIZE_LIMIT", 200*1024),
            "USE_CRC" => COption::GetOptionString("catalog", "1C_USE_CRC", "Y"),
            "USE_ZIP" => COption::GetOptionString("catalog", "1C_USE_ZIP", "Y"),
            "USE_OFFERS" => COption::GetOptionString("catalog", "1C_USE_OFFERS", "N"),
            "USE_IBLOCK_TYPE_ID" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "N"),
            "USE_IBLOCK_PICTURE_SETTINGS" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_PICTURE_SETTINGS", "N"),
            "TRANSLIT_ON_ADD" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "N"),
            "TRANSLIT_ON_UPDATE" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "N"),
            "SKIP_ROOT_SECTION" => COption::GetOptionString("catalog", "1C_SKIP_ROOT_SECTION", "N"),
        )
    );
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>