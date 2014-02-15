<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<div class="contacts_page">
    <?=$arResult["DESCRIPTION"]?>
    <?if(is_array($arResult["ITEMS"])):?>
        <p>Заинтересовавшие Вас товары Вы можете приобрести по следующим адресам:</p>
        <?
            $mapItems = array();
        foreach($arResult["ITEMS"] as $arItem) {
           if(strlen($arItem["PROPERTIES"]["MAP"]["VALUE"]) > 0) {
               $arCoords = explode(",", $arItem["PROPERTIES"]["MAP"]["VALUE"]);
               $longitude = $arCoords[0].".".$arCoords[1];
               $latitude = $arCoords[2].".".$arCoords[3];

               $mapItems[] = array(
                   "NAME" => $arItem["NAME"],
                   "COORDS" => array(
                       "LAT" => $arCoords[0],
                       "LNG" => $arCoords[1]
                   )
               );
           }
        }
        $arPoints = array();
        ?>
        <?if(count($mapItems) > 0):?>
            <div class="map">
                <div id="map" style="width: 858px; height: 302px;">

                </div>
            </div>

            <script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
            <script type="text/javascript">
                ymaps.ready(init);

                var myMap,
                    myPlacemark;

                function init() {
                    myMap = new ymaps.Map ("map", {
                        center: [53.90226539512741,27.561932725156755],
                        zoom : 10
                    });
                    myMap.controls.add(
                        new ymaps.control.ZoomControl()
                    );

                    <?foreach($mapItems as $key=>$mapItem):?>
                        var placemark<?=$key?> = new ymaps.Placemark([<?=$mapItem["COORDS"]["LAT"]?>, <?=$mapItem["COORDS"]["LNG"]?>], {
                            content: '',
                            balloonContent: '<?=$mapItem["NAME"]?>'
                        });
                        myMap.geoObjects.add(placemark<?=$key?>);
                    <?endforeach?>
                };
            </script>
        <?endif?>
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <h5><?=$arItem["NAME"]?></h5>
            <?$p = $arItem["PROPERTIES"];
              $arProps = array(
                  "ADDRESS", "WORK_HOURS", "PHONE", "EMAIL", "ICQ", "SKYPE"
              );
              $displayProps = array();
            ?>
            <a name="store-<?=$p["STORE_ID"]["VALUE"]?>"></a>
            <?foreach($arProps as $pCode) {
                if(is_array($p[$pCode]["VALUE"])) {
                  if(strlen($p[$pCode]["VALUE"]["TEXT"]) > 0)
                      $displayProps[] = "<li><strong>".$p[$pCode]["NAME"].":</strong>".$p[$pCode]["VALUE"]["TEXT"]."</li>";
                } elseif(strlen($p[$pCode]["VALUE"]) > 0) {
                    $displayProps[] = "<li><strong>".$p[$pCode]["NAME"].":</strong>".$p[$pCode]["VALUE"]."</li>";
                }
            }?>
            <?if(count($displayProps) > 0):?>
                <ul>
                    <?=implode("", $displayProps)?>
                </ul>
            <?endif?>
            <div class="photos">
                <?if(is_array($arItem["DETAIL_PICTURE"])):?>
                    <?if(is_array($arItem["DETAIL_PICTURE_SCALED"])):?>
                        <?$detailImageSrc = $arItem["DETAIL_PICTURE_SCALED"]["src"]?>
                    <?else:?>
                        <?$detailImageSrc = $arItem["DETAIL_PICTURE"]["SRC"]?>
                    <?endif?>
                    <ul class="thumbs">
                        <?if(is_array($arItem["PREVIEW_PICTURE_SCALED"])):?>
                            <li><a href="<?=$detailImageSrc?>" rel="lightbox[product<?=$arItem["ID"]?>]"><img src="<?=$arItem["PREVIEW_PICTURE_SCALED"]["src"]?>" /></a></li>
                        <?endif?>

                        <?if(is_array($arItem["MORE_PHOTO"])):?>
                            <?foreach($arItem["MORE_PHOTO"] as $arPhoto):?>
                                <?if(is_array($arPhoto["PREVIEW_PICTURE_SCALED"])):?>
                                    <li><a href="<?=$arPhoto["DETAIL_PICTURE_SCALED"]["src"]?>" rel="lightbox[product<?=$arItem["ID"]?>]"><img src="<?=$arPhoto["PREVIEW_PICTURE_SCALED"]["src"]?>" /></a></li>
                                <?endif?>
                            <?endforeach?>
                        <?endif?>
                    </ul>
                    <div class="clear"></div>
                <?endif?>
            </div><!--/photos-->
        <?endforeach?>
    <?endif?>
</div>