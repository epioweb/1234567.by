<?
error_reporting(E_STRICT);
date_default_timezone_set('Europe/Minsk');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


if ($APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
{ 
    if ( $_POST["send"] == 'Y' )
    {
        $arEventFields = array(
            "ERROR_COMMENT" => $_POST["comment"],
            "ERROR_TEXT" => $_POST["text"],
            "ERROR_PAGE" => $_POST["page"],
        );
        CEvent::Send("CONTENT_ERROR", 's1', $arEventFields, 'Y');
    }
    echo json_encode(array("CODE"=>$APPLICATION->CaptchaGetCode()));
}
else
{
    $APPLICATION->RestartBuffer();
    echo json_encode(array("ERROR"=>"Неверно введено слово с картинки", "CODE"=>$APPLICATION->CaptchaGetCode()));
}
   //echo htmlspecialchars($APPLICATION->CaptchaGetCode());
?>