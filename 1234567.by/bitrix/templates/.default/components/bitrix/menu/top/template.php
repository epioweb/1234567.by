<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?if (!empty($arResult)):?>
    <table>
        <tr>
            <?foreach($arResult as $arItem):
                if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                    continue;?>

                <?if($arItem["SELECTED"] == 1):?>
                    <td><a href="<?=$arItem["LINK"]?>" class="current"><?=$arItem["TEXT"]?><?if(isset($arItem["PRODUCT_COUNT"])):?><sup><?=$arItem["PRODUCT_COUNT"]?></sup><?endif?><span></span></a></td>
                <?else:?>
                    <td><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?><?if(isset($arItem["PRODUCT_COUNT"])):?><sup><?=$arItem["PRODUCT_COUNT"]?></sup><?endif?><span></span></a></td>
                <?endif?>
            <?endforeach?>
        </tr>
    </table>
<?endif?>