<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?$totalItems = count($arResult["ITEMS"])?>
<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="basket <?=$totalItems > 0 ? "full" : ""?>">
    <ul>
        <li>Товаров в корзине: <span><?=$totalItems?></span></li>
        <li class="last">На сумму: <span><?=$totalItems > 0 ? $arResult["TOTAL_SUMM_RUB_FORMATED"] : "0"?></span></li>
    </ul>
</a><!--/basket-->