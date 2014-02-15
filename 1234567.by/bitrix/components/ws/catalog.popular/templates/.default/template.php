<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?// printAdmin($arResult)?>
<?if($arResult["TOTAL_ITEMS"] > 0):?>
    <div class="popular">
        <h4>Популярные товары</h4>
        <?if($arResult["TOTAL_ITEMS"] > 4):?>
            <script>
                $(document).ready(function(){
                    $("#jFlowCarousel").jCarouselLite({
                            btnNext: ".next",
                            btnPrev: ".prev"
                            //circular: false
                        }
                    );
                });
            </script>
        <?endif?>
        <div class="relative">
            <?if($arResult["TOTAL_ITEMS"] > 4):?>
                <a class="prev"></a>
                <a class="next"></a>
            <?endif?>
            <div id="jFlowCarousel" class="carousel">
                <ul class="nostyle">
                    <?foreach($arResult["ITEMS"] as $arItem):?>
                        <li>
                            <div class="ind">
                                <div class="img">
									<?//=$arItem["P"]["COUNTER_OF_VIEWINGS"]["VALUE"]?>
                                    <?if(is_array($arItem["PREVIEW_PICTURE"])):?>
                                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" /></a></div>
                                    <?endif?>
                                <p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></p>
                                <?foreach($arItem["PRICES"] as $arPrice):?>
                                    <strong><?=$arPrice["PRINT_VALUE"]?></strong>
                                <?endforeach?>
                            </div>
                        </li>
                    <?endforeach?>
                </ul>
            </div>
        </div><!--/relative-->
    </div><!--/popular-->
<?endif?>