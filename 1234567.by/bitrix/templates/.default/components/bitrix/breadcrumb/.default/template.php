<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//prent($arParams["ADD_ITEMS"]);

//delayed function must return a string
if(empty($arResult))
    return "";

global $APPLICATION;
$curDir = $APPLICATION->GetCurDir();

if($curDir != "/") {
    $strReturn = "<div class='url'>";

}
for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
    if($arResult[$index]["TITLE"] == "BACK")
        continue;

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    if($index > 0)
        $strReturn .= "  <span>»</span>  ";

    if($arResult[$index]["LINK"] <> "")
        $strReturn .= "<a href='".$arResult[$index]["LINK"]."'>".$title."</a>";
    else
        $strReturn .= $title;
}

if($curDir != "/")
    $strReturn .= "</div>";

return $strReturn;