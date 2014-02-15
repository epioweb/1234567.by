<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if($arResult["ITEMS_COUNT"] > 0):?>
    <?//printObj($arResult["IN_BASKET"])?>
    <div class="filtr">
        <div class="quant">Выводить по  <form action="" name="show_count_form" style="display:inline"><select name="show" onchange="$('.quant form').submit()">
                <?foreach($arParams["SHOW_NUMBERS"] as $count):?>
                    <option value="<?=$count?>" <?=$arParams["PAGE_ELEMENT_COUNT"] == $count ? "selected='selected'" : ""?>><?=$count?></option>
                <?endforeach?>
            </select></form>  товаров на страницу </div>
        <ul class="sort nostyle">
            <li>Сортировать по:</li>
            <?foreach($arResult["SORT_LINKS"] as $arSort):?>
                <li <?=$arSort["CURRENT"] == "Y" ? "class='selected'" : ""?>><a href="<?=$arSort["URL"]?>" <?=$arSort["ORDER"] == "desc" ? "class='reverse'" : ""?>><?=$arSort["NAME"]?></a></li>
            <?endforeach?>
        </ul>
    </div>
    <div class="table">
        <table>
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <tr>
                    <th><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" /></a></th>
                    <td><h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
                        <?if(strlen($arItem["PREVIEW_TEXT"]) > 0):?>
                            <p><?=$arItem["PREVIEW_TEXT"]?></p>
                        <?endif?>
                    </td>
                    <td>
                        <?if($arItem["CATALOG_QUANTITY"] > 0):?>
                            <?foreach($arItem["PRICES"] as $key => $arPrice):?>
                                <h5><?=$arPrice["PRINT_VALUE"]?></h5><h6><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></h6>
                                <?$priceType = $arResult["PRICES"][$key]["ID"]?>
                            <?endforeach?>
                            <?if($priceType):?>
                                <?if(in_array($arItem["ID"], $arResult["IN_BASKET"])):?>
                                    <button class="buy order"><span>Оформить</span></button>
                                <?else:?>
                                    <button class="buy" rel="<?=$arItem["ID"]?>:<?=$priceType?>"><span>Купить</span></button>
                                <?endif?>
                            <?endif?>
                        <?endif?>
                    </td>
                </tr>
            <?endforeach?>
        </table>
    </div>
    <?=$arResult["NAV_STRING"]?>
<?endif?>