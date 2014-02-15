<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult["NavPageCount"] > 1):
    $bDots = false;
    if($arResult["NavPageCount"] > 11):
        // Формируем массив страниц, для которых выводить ссылки
        $arPages = array(1, 2, 3, $arResult["NavPageNomer"]);
        $nHalf = intval($arResult["NavPageCount"] / 2);
        for($i = $arResult["NavPageCount"] - 2; $i <= $arResult["NavPageCount"]; $i++)
            $arPages[] = $i;

        $bDots = true;
    endif?>
    <div class="pager">
        <?=GetMessage("pages")?>:
        <?$bFirst = true;
        $curPage = 1;
        $halfPage = intval($arResult["NavPageCount"] / 2);
        $bRightShownDots = false;
        $bLeftShownDots = false;
        do{?>
            <?if ($curPage == $arResult["NavPageNomer"]):?>
                <?if($arResult["NavPageNomer"] > 3 && ($arResult["NavPageNomer"] < ($arResult["NavPageCount"] - 2)) && !$bShownInput):?>
                    <?$bShownInput = true;?>
                    ...
                <?else:?>
                    <a class="current" href="<?=$arResult["sUrlPath"]?>"><?=$curPage?></a>
                <?endif?>
            <?elseif($curPage == 1 && $arResult["bSavePage"] == false):?>
                <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$curPage?></a>
            <?else:?>
                <?if($bDots):?>
                    <?if(in_array($curPage, $arPages)):?>
                        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$curPage?>"><?=$curPage?></a>
                    <?elseif(!$bDotsShown):?>
                        <?$bDotsShown = true;?>
                        ...
                    <?endif?>
                <?else:?>
                    <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$curPage?>"><?=$curPage?></a>
                <?endif?>
            <?endif;?>
            <?$curPage++;
        } while($curPage <= $arResult["NavPageCount"]);?>
    </div>
<?endif?>