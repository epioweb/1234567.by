<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?if(is_array($arResult["ITEMS"])):?>
    <div class="news_page">
        <h3>Новости</h3>
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <div class="item">
                <h6><?=$arItem["DISPLAY_ACTIVE_FROM"]?></h6>
                <h5><?=$arItem["NAME"]?></h5>
                <div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?>
                    <?if(strlen($arItem["DETAIL_TEXT"]) > 0):?>
                        <div class="detail-text" style="display:none; margin-top:10px;">
                            <?=$arItem["DETAIL_TEXT"]?>
                        </div>
                        <a class="more" href="#"> Далее</a>
                    <?endif?>
                </div>
            </div>
        <?endforeach?>
    </div><!--/news-->
<?endif?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?>
<?endif;?>
