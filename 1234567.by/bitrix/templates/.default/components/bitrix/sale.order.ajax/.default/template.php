<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//printObj($arResult)?>
<a name="order_fform"></a>

<div id="order_form_div" class="order-checkout form">
    <NOSCRIPT>
        <div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
    </NOSCRIPT>
    <?if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
    {
        if(!empty($arResult["ERROR"]))
        {
            foreach($arResult["ERROR"] as $v)
                echo ShowError($v);
        }
        elseif(!empty($arResult["OK_MESSAGE"]))
        {
            foreach($arResult["OK_MESSAGE"] as $v)
                echo ShowNote($v);
        }

        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
    }
    else
    {
        if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y")
        {
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
        }
        else
        {
            ?>
            <?if($_POST["is_ajax_post"] != "Y")
            {
                ?><form action="/personal/order/make/index.php" method="POST" name="ORDER_FORM" id="ORDER_FORM">
                <div id="order_form_content">
                <?
            }
            else
            {
                $APPLICATION->RestartBuffer();
            }
            ?>
            <?=bitrix_sessid_post()?>
            <?
            if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
            {
                foreach($arResult["ERROR"] as $v)
                    echo ShowError($v);

                ?>
                <script>
                    top.BX.scrollToNode(top.BX('ORDER_FORM'));
                </script>
                <?
            }

            if(count($arResult["PERSON_TYPE"]) > 1)
            {
                ?>
                <b><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></b>
                <table class="sale_order_full_table">
                <tr>
                <td>
                <?
                foreach($arResult["PERSON_TYPE"] as $v)
                {
                    ?><input type="radio" id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onClick="submitForm()"> <label for="PERSON_TYPE_<?= $v["ID"] ?>"><?= $v["NAME"] ?></label><br /><?
                }
                ?>
                <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
                </td></tr></table>
                <br /><br />
                <?
            }
            else
            {
                if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0)
                {
                    ?>
                    <input type="hidden" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
                    <input type="hidden" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
                    <?
                }
                else
                {
                    foreach($arResult["PERSON_TYPE"] as $v)
                    {
                        ?>
                        <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">
                        <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
                        <?
                    }
                }
            }

            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");

            if($_POST["is_ajax_post"] != "Y"):?>
                    </div>
                    <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
                    <input type="hidden" name="profile_change" id="profile_change" value="N">
                    <input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
                    <button name="submitbutton"><?=GetMessage("SOA_TEMPL_BUTTON")?></button>
                </form>
                <?if($arParams["DELIVERY_NO_AJAX"] == "N"):?>
                    <script language="JavaScript" src="/bitrix/js/main/cphttprequest.js"></script>
                    <script language="JavaScript" src="/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js"></script>
                <?endif;?>
            <?else:?>
                <script>
                    top.BX('confirmorder').value = 'Y';
                    top.BX('profile_change').value = 'N';
                </script>
                <?die();?>
            <?endif;
        }
    }?>
</div>