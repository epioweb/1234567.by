<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?//printObj($arResult)?>
<?if(!is_array($arResult["ITEMS"]))
	return false;?>

<?if(is_array($arResult["ITEMS"])):?>
    <div class="slider" style="background: url(/bitrix/templates/.default/ajax/images/wait.gif) no-repeat center center;">
        <div class="prev"></div>
        <div class="next"></div>
        <?$indexPosition = 1;?>
        <?$totalCnt = count($arResult["ITEMS"])?>
        <div class="slider-container">
            <div class="item-container">
                <?foreach($arResult["ITEMS"] as $key=>$arItem):?>
                    <?$class = $indexPosition == 1 ? "first-element active" : (($indexPosition == $totalCnt) ? "last-element" : "");?>
                    <div class="item <?=$class?> float_left" id="banner<?=$key?>">
                        <div class="image">
                            <?if(is_array($arItem["PICTURE"])):?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>" class="<?=$class?>" style="display:block; height: 100%; background:url(<?=$arItem["PICTURE"]["SRC"]?>) no-repeat center center; visibility: hidden; opacity: 0;">
                                    <img src="<?=$arItem["PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arItem["PICTURE"]["WIDTH"]?>" height="<?=$arItem["PICTURE"]["HEIGHT"]?>" style="visibility: hidden" />
                                </a>
                                <?$indexPosition++;?>
                            <?endif?>
                        </div>
                    </div><!--/item-->
                <?endforeach?>
                <div class="clear"></div>
            </div>
        </div>
        <ul class="controls nostyle">
            <?$indexPosition = 1;?>
            <?foreach($arResult["ITEMS"] as $key=>$arItem):?>
                <li <?=$indexPosition == 1 ? "class='selected'" : ""?> rel="<?=$key?>"></li>
                <?$indexPosition++;?>
            <?endforeach?>

        </ul>
    </div><!--/slider-->
<?endif?>