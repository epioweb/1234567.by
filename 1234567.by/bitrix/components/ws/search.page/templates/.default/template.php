<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?//printObj($arParams)?>
<div class="search search-page">
    <form action="/search/">
        <div class="head">
            <input type="text" value="<?echo strlen($arParams["~Q"]) ? $arParams["~Q"]:"";?>" name="q" />
            <button onclick="document.getElementById('searchForm').submit();"><span>Найти</span></button>
        </div>
        <div style="margin-top:5px;">
            <input type="radio" name="in_stock" id="show-all" value="N" <?=$arParams["IN_STOCK"] == "N" ? "checked='checked'" : ""?> /> <label for="show-all">отображать все</label>
            <input type="radio" name="in_stock" id="show-instock" value="Y" <?=$arParams["IN_STOCK"] == "Y" ? "checked='checked'" : ""?> /> <label for="show-instock">только в наличии</label>
        </div>
    </form>
</div>
<?if(strlen($arParams["~Q"]) > 0):?>
    <?if(count($arResult["ITEMS"]) > 0):?>
        <div class="filtr">
            <div class="quant">Выводить по  <form action="" name="show_count_form" style="display:inline"><select name="show" onchange="$('.quant form').submit()">
                <?foreach($arParams["SHOW_NUMBERS"] as $count):?>
                    <option value="<?=$count?>" <?=$arParams["PAGE_ELEMENT_COUNT"] == $count ? "selected='selected'" : ""?>><?=$count?></option>
                <?endforeach?>
                </select>
                <input type="hidden" name="in_stock" value="<?=$arParams["IN_STOCK"] == "Y" ? "Y" : "N"?>" />
                <input type="hidden" name="q" value="<?=$arParams["~Q"]?>" />
                </form>
            </div>

            <ul class="sort nostyle" style="width:400px;">
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
                                <p><?=$arItem["~PREVIEW_TEXT"]?></p>
                            <?endif?>
                        </td>
                        <td>
                            <?if($arItem["QUANTITY"] > 0):?>
                                <h5><?=$arItem["PRICES"]["PRINT_VALUE"]?></h5><h6><?=$arItem["PRICES"]["PRINT_USD_VALUE"]?></h6>
                                <?if(in_array($arItem["ID"], $arResult["IN_BASKET"])):?>
                                    <button class="buy order"><span>Оформить</span></button>
                                <?else:?>
                                    <button class="buy" rel="<?=$arItem["ID"]?>:<?=$arParams["PRICE_CODE"]?>"><span>Купить</span></button>
                                <?endif?>
                            <?endif?>
                        </td>
                    </tr>
                <?endforeach?>
            </table>
        </div>
        <?=$arResult["NAV_STRING"]?>
    <?else:?>
        <?=ShowStyledError("По вашему запросу ничего не найдено")?>
    <?endif?>
<?else:?>
    <?=ShowStyledError("Пустой поисковый запрос. Введите критерий поиска")?>
<?endif?>