<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
{
	//printAdmin($arPaySystem);
    if(count($arResult["PAY_SYSTEM"]) == 1):?>
        <input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
    <?else:
        if (!isset($_POST['PAY_CURRENT_ACCOUNT']) OR $_POST['PAY_CURRENT_ACCOUNT'] == "N"):?>
            <label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>">
                <?= $arPaySystem["PSA_NAME"] ?>
            </label>
            <input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?>>
        <?endif;
    endif;
}
?>