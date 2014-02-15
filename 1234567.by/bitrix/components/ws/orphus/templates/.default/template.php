<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="attention-area">
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
            "AREA_FILE_SHOW" => "sect",
            "AREA_FILE_SUFFIX" => "orphus",
            "EDIT_TEMPLATE" => ""
        ),
        false
    );?>
</div>

<div class="hidden orphus-form">
    <a href="#send-note" id="send-note-click"></a>
    <form class="send-note-window" id="send-note" action="">
        <div class="preview"></div>
        <div class="input-block">
            <label for="comments">Описание ошибки</label><br/>
            <textarea id="send-note-textarea" name="text" rows="10"></textarea>
        </div>
        <? if (strLen($arResult["CAPTCHA_CODE"]) > 0):?>
            <div class="input-block">
                <div class="captcha-input">
                    <label for="captcha_word">Код с картинки:<span class="required">*</span></label><br/>
                    <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
                    <input type="text" size="30" name="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" />
                    <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" class="capch_img" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" title="Обновить картинку"/>
                    <a href="#" class="refresh-captcha">Другая картинка</a><br/>
                </div>
            </div>
        <? endif;?>
        <div class="submit-block">
            <input type="hidden" name="hidden" name="submit" value="Y" />
            <input type="submit" name="submit" class="buy" value=" Отправить " />
            <input type="button" name="cancel-button" class="buy" value=" Отмена " />
        </div>
    </form>
</div>