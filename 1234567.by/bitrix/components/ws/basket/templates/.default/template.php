<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
    <div class="basket_page">
        <div class="basket_table">
            <?/*<h3>Корзина</h3>*/?>
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
                        <tr rel="<?=$arItem["PRODUCT_ID"]?>">
                            <th>
                                <?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?>
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
                                    <?foreach($arItem["PRICES"] as $arPrice):?>
                                        <h5><?=$arPrice["PRINT_VALUE"]?></h5><h6><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></h6></td>
                                    <?endforeach?>
                                <?endif?>

                            <td><div class="quant"><div class="pl">+</div><div class="mn">-</div><div class="num"><input type="text" name="q" value="<?=$arItem["QUANTITY"]?>" style="border: none; padding: 6px; width: 28px; text-align: center" /></div></td>
                            <td class="total-price">
                                <?if(is_array($arItem["TOTAL_PRICES"])):?>
                                    <?foreach($arItem["TOTAL_PRICES"] as $arPrice):?>
                                        <h5><?=$arPrice["PRINT_VALUE"]?></h5><h6><?=$arPrice[$arParams["SEC_CURRENCY"]]["PRINT_VALUE"]?></h6></td>
                                    <?endforeach?>
                                <?endif?>
                            </td>
                            <td><button class="buy delete">Удалить</button></td>
                        </tr>
                    <?endforeach?>
                    <tfoot>
                        <th colspan="4">Итого:</th>
                        <td colspan="2" class="total_price">
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
        <?$APPLICATION->IncludeComponent("ws:sale.order.ajax", ".default", array(
	"PAY_FROM_ACCOUNT" => "N",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_AJAX" => "N",
	"TEMPLATE_LOCATION" => "popup",
	"PROP_1" => "",
	"PATH_TO_BASKET" => "/personal/cart/",
	"PATH_TO_PERSONAL" => "/personal/order/",
	"PATH_TO_PAYMENT" => "/personal/order/payment/",
	"PATH_TO_ORDER" => "/personal/order/make/",
	"SET_TITLE" => "N",
	"DELIVERY2PAY_SYSTEM" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
    </div>
<?else:?>
<?=ShowStyledNotice(GetMessage("SALE_NO_ACTIVE_PRD"))?>
<?endif?>