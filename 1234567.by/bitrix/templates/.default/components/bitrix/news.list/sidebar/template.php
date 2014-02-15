<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?if(is_array($arResult["ITEMS"])):?>
    <div class="news">
        <h4><a href="/news/"><?=$arResult["NAME"]?></a></h4>
        <ul>
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <li><h6><?=$arItem["DISPLAY_ACTIVE_FROM"]?></h6>
                    <h5><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h5>
                    <p><?=HTMLToTxt(TruncateText($arItem["PREVIEW_TEXT"], 100))?></p>
                </li>
            <?endforeach?>
        </ul>

        <div class="news-list-link"><a href="/news/" >новости компании</a></div>
    </div><!--/news-->
<?endif?>
