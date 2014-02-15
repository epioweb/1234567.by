<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="search search-page">
    <form action="" method="get">
        <div class="head">
			<input type="hidden" name="tags" value="<?echo $arResult["REQUEST"]["TAGS"]?>" />
			
            <input type="text" value="<?=$arResult["REQUEST"]["QUERY"]?>" name="q" />
			<input class="button" type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
        </div>
        <div class="stock" style="margin-top:5px;">
				<input type="radio" name="in_stock" onchange="$('.search form').submit()" id="show-all" value="N" <?=$arParams["IN_STOCK"] == "N" ? "checked='checked'" : ""?> /> <label for="show-all">отображать все</label>
				<input type="radio" name="in_stock" onchange="$('.search form').submit()" id="show-instock" value="Y" <?=$arParams["IN_STOCK"] == "Y" ? "checked='checked'" : ""?> /> <label for="show-instock">только в наличии</label>
				
				<input type="hidden" name="o" value="<?=$_REQUEST["o"]?>" />
				<input type="hidden" name="f" value="<?=$_REQUEST["f"]?>" />
        </div>
    </form>
</div>
<pre>
<?//= var_dump($arResult);?>
</pre>
    <?if(count($arResult["SEARCH"]) > 0):?>
        <div class="filtr">
            <div class="quant">Выводить по  <form action="" name="show_count_form" style="display:inline"><select name="show" onchange="$('.quant form').submit()">
                <?foreach($arParams["SHOW_NUMBERS"] as $count):?>
                    <option value="<?=$count?>" <?=$arParams["PAGE_RESULT_COUNT"] == $count ? "selected='selected'" : ""?>><?=$count?></option>
                <?endforeach?>
                </select>
                <input type="hidden" name="in_stock" value="<?=$arParams["IN_STOCK"] == "Y" ? "Y" : "N"?>" />
                <input type="hidden" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" />
                </form>
            </div>

            <ul class="sort nostyle">
                <li>Сортировать по:</li>
                <?foreach($arResult["SORT_LINKS"] as $arSort):?>
                    <li <?=$arSort["CURRENT"] == "Y" ? "class='selected'" : ""?>><a href="<?=$arSort["URL"]?>" <?=$arSort["ORDER"] == "desc" ? "class='reverse'" : ""?>><?=$arSort["NAME"]?></a></li>
                <?endforeach?>
            </ul>
        </div>
        <div class="table">
            <table>

                <?foreach($arResult["SEARCH"] as $arItem):?>
					
					<?//if ($arItem["QUANTITY"]>0):?>
                    <tr>

                        <th><a href="<?=$arItem["URL"]?>"><img src="<?=$arItem["PICTURE_SRC"]?>" /></a></th>
                        <td><h4><a href="<?=$arItem["URL"]?>"><?=$arItem["TITLE_FORMATED"]?></a></h4>
                            <?if(strlen($arItem["BODY_FORMATED"]) > 0):?>
                                <p><?=$arItem["BODY_FORMATED"]?></p>
                            <?endif?>
                        </td>
                        <td>
                            <?if(($arItem["QUANTITY"] > 0)):?>
                                <h5><?=$arItem["PRICES"]["PRINT_VALUE"]?></h5><h6><?=$arItem["PRICES"]["PRINT_USD_VALUE"]?></h6>

                                <?if(in_array($arItem["ITEM_ID"], $arResult["IN_BASKET"])):?>
                                    <button class="buy order"><span>Оформить</span></button>
                                <?else:?>
                                    <button class="buy" rel="<?=$arItem["ITEM_ID"]?>:3"><span>Купить</span></button>
                                <?endif?>
                            <?endif?>
                        </td>
                    </tr>
					<?//endif;?>
                <?endforeach?>
            </table>
        </div>
        <?=$arResult["NAV_STRING"]?>
    <?else:?>
        <?=ShowStyledError("По вашему запросу ничего не найдено")?>
    <?endif?>
