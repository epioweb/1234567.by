<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?//printObj($arResult)?>
<?$bOk = false;?>
<?if(count($arResult["ITEMS"]) > 0):?>
    <?foreach($arResult["ITEMS"] as $arItem):?>
        <th>
            <?if(is_array($arItem["PREVIEW_PICTURE"])):?>
                <div class="img"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="_blank"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" /></a></div>
            <?endif?>
        </th>
        <td><p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arItem["NAME"]?></a></p></td>
        <td><h5><?=$arItem["PRICES"]["PRINT_USD_VALUE"]?></h5> <h6><?=$arItem["PRICES"]["PRINT_VALUE"]?></h6></td>#cell#
        <?=$arItem["DETAIL_PAGE_URL"]?>#end#
	<?endforeach?>
	<?$bOk = true;?>
<?endif?>

<?if(!$bOk):?>
    <td>
	    <?=GetMessage("SEARCH_NOTHING_TO_FOUND");?>
    </td>
<?endif;?>
#end#