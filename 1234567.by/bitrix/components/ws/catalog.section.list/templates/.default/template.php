<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?//printObj($arParams)?>
<?if(count($arResult["SECTIONS"]) > 0):?>
    <div class="list">
        <table>
            <tr>
                <?$rowPerColumn = ceil(count($arResult["SECTIONS"]) / 3);?>
                <?$arColSections = array_chunk($arResult["SECTIONS"], $rowPerColumn);?>
                <?foreach($arColSections as $key=>$arSections):?>
                    <?if($key > 0):?>
                        <th><div>&nbsp;</div></th>
                    <?endif?>
                    <td>
                        <?foreach($arSections as $key=>$arSection):?>
                            <?$subCnt = count($arSection["CHILDS"]);?>
                            <div class="section-container <?=$subCnt == 0 ? "description-only" : ""?>">
                                <?if($subCnt == 0):?>
                                    <div class="overflow-container">
                                <?endif?>
                                <h4><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a><sup><?=$arSection["UF_QUANTITY"]?></sup></h4>
                                <div class="menu-container">
                                    <?if($subCnt > 0):?>
                                        <?$subMenuOpened = false;?>
                                        <?$totalStringLength = 0;?>
                                        <?foreach($arSection["CHILDS"] as $subKey=>$arChild):?>
                                            <?if($subKey >= ($arParams["MAX_VISIBLE_SUB_SECTIONS"] - 1) || (strlen($arChild["NAME"]) + $totalStringLength > 80)):?>
                                                <?if(!$subMenuOpened):?>
                                                    <?$subMenuOpened = true;?>
                                                    <div class="relative"><span><a href="#">...&raquo;</a></span>
                                                        <ul>
                                                <?endif?>
                                                <li><a href="<?=$arChild["SECTION_PAGE_URL"]?>"><?=$arChild["NAME"]?></a></li>
                                            <?else:?>
                                                <a href="<?=$arChild["SECTION_PAGE_URL"]?>"><?=$arChild["NAME"]?></a>
                                            <?endif?>
                                            <?$totalStringLength += strlen($arChild["NAME"]);?>
                                        <?endforeach?>
                                        <?if($subMenuOpened):?>
                                            </ul>
                                            </div>
                                        <?endif?>
                                    <?elseif(strlen($arSection["DESCRIPTION"]) > 0):?>
                                        <?=TruncateText($arSection["DESCRIPTION"], 120)?>
                                    <?endif?>
                                </div>
                                <?if($subCnt == 0):?>
                                    </div>
                                <?endif?>
                            </div>
                        <?endforeach?>
                    </td>
                <?endforeach?>
            </tr>
        </table>
    </div><!--/list-->
<?endif?>