<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?//printAdmin($arResult)?>
<?

	global $USER;
?>
<?if(is_array($arResult["ITEM"])):?>
    <?
    $i = $arResult["ITEM"];
    $c = $arResult["CATALOG"]?>
    <div class="product">
        <div class="gallery">
            <div class="image">
                <?if(is_array($i["MIDDLE_PICTURE"])):?>
                    <ul class="middle-photos" style="width: <?=(count($i["MORE_PHOTO"]) + 1) * 300?>px">
                        <li>
                            <a href="<?=$i["DETAIL_PICTURE"]["SRC"]?>" id="photo-0" rel="lightbox[product]"><img src="<?=$i["MIDDLE_PICTURE"]["SRC"]?>" title="<?=$i["NAME"]?>" alt="<?=$i["NAME"]?>" /></a>
                        </li>
                        <?if(count($i["MORE_PHOTO"]) > 0):?>
                            <?foreach($i["MORE_PHOTO"] as $key => $arPhoto):?>
                                <li><a href="<?=$arPhoto["DETAIL_PICTURE"]["SRC"]?>" rel="lightbox[product]" id="photo-<?=$key + 1?>"><img src="<?=$arPhoto["MIDDLE_PICTURE"]["SRC"]?>" title="<?=$i["NAME"]?>" alt="<?=$i["NAME"]?>" /></a></li>
                            <?endforeach?>
                        <?endif?>
                    </ul>
                <?endif?>
            </div>
            <?if(count($i["MORE_PHOTO"]) > 0):?>
                <ul class="thumbs nostyle">
                    <li class="active"><a href="<?=$i["DETAIL_PICTURE"]["SRC"]?>" rel="photo-0"><img src="<?=$i["PREVIEW_PICTURE"]["SRC"]?>" /></a></li>
                    <?foreach($i["MORE_PHOTO"] as $key => $arPhoto):?>
                        <li><a href="<?=$arPhoto["DETAIL_PICTURE"]["SRC"]?>" rel="photo-<?=$key + 1?>"><img src="<?=$arPhoto["PREVIEW_PICTURE"]["SRC"]?>" /></a></li>
                    <?endforeach?>
                </ul>
            <?endif?>
        </div><!--/gallery-->
        <div class="description">
            <h3><?=$i["NAME"]?></h3>
            <?if($c["CATALOG_QUANTITY"] > 0):?>
                <?foreach($c["PRICES"] as $key => $arPrice):?>
                    <?$priceType = $c["PRICE_TYPE"][$key]["ID"]?>
                    <div class="price">
                        <h6><?=$arPrice["PRINT_VALUE"]?> <span><?=$arPrice["USD"]["PRINT_VALUE"]?></span></h6>
                        <?if($priceType):?>
                            <?if(in_array($i["ID"], $arResult["IN_BASKET"])):?>
                                <button class="buy order"><span>Оформить</span></button>
                            <?else:?>
                                <button class="buy" rel="<?=$i["ID"]?>:<?=$priceType?>"><span>Купить</span></button>
                            <?endif?>
                        <?endif?>
                    </div>
                <?endforeach?>
                <h5>Есть в наличии:</h5>
                <?if(is_array($c["STORES"])):?>
                    <table class="nostyle">
                        <?foreach($c["STORES"] as $arStore):?>
                            <?if(intval($arStore["AMOUNT"]) > 0):?>
                                <tr>
                                    <?if($arStore["AMOUNT"] > 10) {
                                        $mess = GetMessage("IN_STOCK_MORE_10");
                                        $title = GetMessage("IN_STOCK_MORE_10_TITLE");
                                    } elseif($arStore["AMOUNT"] > 3) {
                                        $mess = GetMessage("IN_STOCK_MORE_3");
                                        $title = GetMessage("IN_STOCK_MORE_3_TITLE");
                                    } else {
                                        $mess = $title = $arStore["AMOUNT"];
                                    }?>
                                    <td nowrap="nowrap" title="<?=$title?>"><span class="blue"><strong><?=$mess?></strong> шт.</span></td>
                                    <td>» </td>
									<td><a href="/contacts/#store-<?=$arStore["STORE_ID"]?>"><?=(strlen($arStore["STORE_ADDR"]) > 2) ? $arStore["STORE_ADDR"] : $arStore["STORE_NAME"];?></a></td>
                                </tr>
                            <?endif?>
                        <?endforeach?>
                    </table>
                <?endif?>
            <?else:?>
			<!-- кнопка подписки на товар -->
				<button class="subscribe" rel="<?=$i["ID"]?>:<?=$priceType?>"><span>Сообщить о появлении  продаже</span></button>
			<!-- /кнопка подписки на товар -->
                <?=ShowStyledError(GetMessage("OUT_OF_STOCK"), "display:inline-block;")?>
            <?endif?>

            <?if(strlen($i["DETAIL_TEXT"]) > 0):?>
                <h5>Описание товара:</h5>
                <div>
                    <?=$i["DETAIL_TEXT"]?>
                </div>
            <?endif?>

            <?$APPLICATION->IncludeComponent("ws:orphus", ".default", array(),
                false
            );?>
        </div>

    </div>
<!-- попап подписки на товар -->
	 <div id="subscribe_form" class="popup">
		<p class="close-popup"><img src="/i/close_sm.png"></p>
		<h3>Введите адрес электронной почты</h3>
		<div>
			<p id="error_email" style="color:red; display:none;">Адрес введен не корректно!</p>
			<p>
				<input type="text" id="email" value="" placeholder="E-mail" style="width:100%;" />
			</p>	
			<p style="text-align:center;">
				<button id="subscribe_btn">Подписаться</button>
			</p>
				<input type="hidden" id="product_id" value="<?=$arResult["ITEM"]["ID"]?>" />
		</div>
	</div>
	<div class="popup_shadow"></div>
<!-- /попап подписки на товар -->
<?else:?>
    <?=ShowStyledError(GetMessage("ELEMENT_NOT_FOUND"))?>
<?endif?>