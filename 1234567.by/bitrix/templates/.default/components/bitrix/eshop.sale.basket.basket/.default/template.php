<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
    <div class="basket_page">
        <h3>Корзина</h3>
        <h5>Товары:</h5>
        <div class="table">
            <table>
                <thead>
                    <th>Фото</th>
                    <th>Наименование</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                    <th></th>
                </thead>
                <?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arItem):?>
                    <tr>
                        <th>
                            <?if (strlen($arItem["DETAIL_PAGE_URL"])>0):?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                            <?endif;?>
                            <?if (strlen($arItem["DETAIL_PICTURE"]["SRC"]) > 0) :?>
                                <img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"] ?>"/>
                            <?else:?>
                                <img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arItem["NAME"] ?>"/>
                            <?endif?>
                            <?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?>
                                </a>
                            <?endif;?>
                        </th>
                        <td><p><?=$arItem["NAME"]?></p></td>
                        <td>
                            <?if(is_array($arItem["PRICES"])):?>
                                <?foreach($arItem["PRICE"] as $arPrice):?>
                                    <h5><?=$arPrice["PRINT_VALUE"]?></h5><h6><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></h6></td>
                                <?endforeach?>
                            <?endif?>

                        <td><div class="quant"><div class="pl">+</div><div class="mn">-</div><div class="num"><input type="text" name="q" value="<?=$arItem["QUANTITY"]?>" style="border: none; padding: 6px; width: 28px; text-align: center" /></div></td>
                        <td>
                            <?if(is_array($arItem["TOTAL_PRICES"])):?>
                                <?foreach($arItem["TOTAL_PRICES"] as $arPrice):?>
                                    <h5><?=$arPrice["PRINT_VALUE"]?></h5><h6><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></h6></td>
                                <?endforeach?>
                            <?endif?>
                        </td>
                        <td><button class="buy">Удалить</button></td>
                    </tr>
                <?endforeach?>
                <tfoot>
                    <th colspan="4">Итого:</th>
                    <td colspan="2">
                        <?if(is_array($arResult["TOTAL_PRICES"])):?>
                            <?foreach($arResult["TOTAL_PRICES"] as $arPrice):?>
                                <?=$arPrice["PRINT_VALUE"]?>  <span><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></span>
                            <?endforeach?>
                        <?else:?>
                            <?=ShowStyledError("Не рассчитана итоговая стоимость");?>
                        <?endif?>
                        </td>
                </tfoot>
            </table>
        </div>
    </div>
<?else:?>
    <?=ShowStyledNotice(GetMessage("SALE_NO_ACTIVE_PRD"))?>
<?endif?>