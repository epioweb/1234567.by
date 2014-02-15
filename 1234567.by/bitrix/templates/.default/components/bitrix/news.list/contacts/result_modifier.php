<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arParams["MAX_IMAGE_WIDTH"] = 700;
$arParams["MAX_IMAGE_HEIGHT"] = 500;

$arParams["MAX_PREVIEW_IMAGE_WIDTH"] = 167;
$arParams["MAX_PREVIEW_IMAGE_HEIGHT"] = 111;

if(is_array($arResult["ITEMS"]))
{
    foreach($arResult["ITEMS"] as &$arItem) {
        if(is_array($arItem["DETAIL_PICTURE"])) {
            if($arItem["DETAIL_PICTURE"]["WIDTH"] > $arParams["MAX_IMAGE_WIDTH"] || $arItem["DETAIL_PICTURE"]["HEIGHT"] > $arParams["MAX_IMAGE_HEIGHT"]) {
                $arItem["DETAIL_PICTURE_SCALED"] = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array('width'=>$arParams["MAX_IMAGE_WIDTH"], 'height'=>$arParams["MAX_IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_EXACT, true);
                $arItem["PREVIEW_PICTURE_SCALED"] = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array('width'=>$arParams["MAX_PREVIEW_IMAGE_WIDTH"], 'height'=>$arParams["MAX_PREVIEW_IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_EXACT, true);
            }
        }

        if(is_array($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
            $arPhotos = array();
            foreach($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $key => $photoID) {
                $arPhoto = CFile::GetFileArray($photoID);
                if(is_array($arPhoto)) {
                    $arPhoto["DETAIL_PICTURE_SCALED"] = CFile::ResizeImageGet($arPhoto, array('width'=>$arParams["MAX_IMAGE_WIDTH"], 'height'=>$arParams["MAX_IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_EXACT, true);
                    $arPhoto["PREVIEW_PICTURE_SCALED"] = CFile::ResizeImageGet($arPhoto, array('width'=>$arParams["MAX_PREVIEW_IMAGE_WIDTH"], 'height'=>$arParams["MAX_PREVIEW_IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_EXACT, true);
                    $arPhotos[] = $arPhoto;
                }
            }
            if(count($arPhotos) > 0)
                $arItem["MORE_PHOTO"] = $arPhotos;
        }
    }
}