<?
error_reporting(E_STRICT);
date_default_timezone_set('Europe/Minsk');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
echo htmlspecialchars($APPLICATION->CaptchaGetCode());
?>