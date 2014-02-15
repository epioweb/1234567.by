<?
AddEventHandler("main", "OnUserTypeBuildList", array("UFExtPhoto", "GetUserTypeDescription"));

/**
 * @author mac
 * класс suggest привязки разделов к разделам
 */
class UFExtPhoto
{
    /**
     * Обработчик события OnUserTypeBuildList.
     *
     * <p>Эта функция регистрируется в качестве обработчика события OnUserTypeBuildList.
     * Возвращает массив описывающий тип пользовательских свойств.</p>
     * <p>Элементы массива:</p>
     * <ul>
     * <li>USER_TYPE_ID - уникальный идентификатор
     * <li>CLASS_NAME - имя класса методы которого формируют поведение типа
     * <li>DESCRIPTION - описание для показа в интерфейсе (выпадающий список и т.п.)
     * <li>BASE_TYPE - базовый тип на котором будут основаны операции фильтра (int, double, string, date, datetime)
     * </ul>
     * @return array
     * @static
     */
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "UFExtPhoto",
            "CLASS_NAME" => "UFExtPhoto",
            "DESCRIPTION" => "Фото ext",
            "BASE_TYPE" => "string",
        );
    }

    /**
     * Эта функция вызывается при добавлении нового свойства.
     *
     * <p>Эта функция вызывается для конструирования SQL запроса
     * создания колонки для хранения не множественных значений свойства.</p>
     * <p>Значения множественных свойств хранятся не в строках, а столбиках (как в инфоблоках)
     * и тип такого поля в БД всегда text.</p>
     * @param array $arUserField Массив описывающий поле
     * @return string
     * @static
     */
    function GetDBColumnType($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    /**
     * Эта функция вызывается перед сохранением метаданных свойства в БД.
     *
     * <p>Она должна "очистить" массив с настройками экземпляра типа свойства.
     * Для того что бы случайно/намеренно никто не записал туда всякой фигни.</p>
     * @param array $arUserField Массив описывающий поле. <b>Внимание!</b> это описание поля еще не сохранено в БД!
     * @return array Массив который в дальнейшем будет сериализован и сохранен в БД.
     * @static
     */
    function PrepareSettings($arUserField)
    {
        //printObj($arUserField); die();
        $iblockType = trim($arUserField["SETTINGS"]["IBLOCK_TYPE"]);
        $iblockId = intval($arUserField["SETTINGS"]["IBLOCK_ID"]);

        $size = intval($arUserField["SETTINGS"]["SIZE"]);
        $rows = intval($arUserField["SETTINGS"]["ROWS"]);
        $min = intval($arUserField["SETTINGS"]["MIN_LENGTH"]);
        $max = intval($arUserField["SETTINGS"]["MAX_LENGTH"]);

        return array(
            "SIZE" =>  ($size <= 1? 20: ($size > 255? 225: $size)),
            "ROWS" =>  ($rows <= 1?  1: ($rows >  50?  50: $rows)),
            "REGEXP" => $arUserField["SETTINGS"]["REGEXP"],
            "MIN_LENGTH" => $min,
            "MAX_LENGTH" => $max,
            "DEFAULT_VALUE" => $arUserField["SETTINGS"]["DEFAULT_VALUE"],
            "IBLOCK_TYPE" =>  $iblockType,
            "IBLOCK_ID" =>  $iblockId
        );
    }

    /**
     * Эта функция вызывается при выводе формы настройки свойства.
     *
     * <p>Возвращает html для встраивания в 2-х колоночную таблицу.
     * в форму usertype_edit.php</p>
     * <p>т.е. tr td bla-bla /td td edit-edit-edit /td /tr </p>
     * @param array $arUserField Массив описывающий поле. Для нового (еще не добавленного поля - <b>false</b>)
     * @param array $arHtmlControl Массив управления из формы. Пока содержит только один элемент NAME (html безопасный)
     * @return string HTML для вывода.
     * @static
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        $result = '';

        if($bVarsFromForm)
            $iblock_id = $GLOBALS[$arHtmlControl["NAME"]]["IBLOCK_ID"];
        elseif(is_array($arUserField))
            $iblock_id = $arUserField["SETTINGS"]["IBLOCK_ID"];
        else
            $iblock_id = "";
        if(CModule::IncludeModule('iblock'))
        {
            $result .= '
            <tr valign="top">
                <td>'.GetMessage("USER_TYPE_IBSEC_DISPLAY").':</td>
                <td>
                    '.GetIBlockDropDownList($iblock_id, $arHtmlControl["NAME"].'[IBLOCK_TYPE_ID]', $arHtmlControl["NAME"].'[IBLOCK_ID]').'
                </td>
            </tr>
            ';
        }
        else
        {
            $result .= '
            <tr valign="top">
                <td>'.GetMessage("USER_TYPE_IBSEC_DISPLAY").':</td>
                <td>
                    <input type="text" size="6" name="'.$arHtmlControl["NAME"].'[IBLOCK_ID]" value="'.htmlspecialchars($value).'">
                </td>
            </tr>
            ';
        }

        if($bVarsFromForm)
            $value = intval($GLOBALS[$arHtmlControl["NAME"]]["ROWS"]);
        elseif(is_array($arUserField))
            $value = intval($arUserField["SETTINGS"]["ROWS"]);
        else
            $value = 1;
        if($value < 1) $value = 1;
        $result .= '
        <tr valign="top">
            <td>'.GetMessage("USER_TYPE_STRING_ROWS").':</td>
            <td>
                <input type="text" name="'.$arHtmlControl["NAME"].'[ROWS]" size="20"  maxlength="20" value="'.$value.'">
            </td>
        </tr>
        ';

        if($bVarsFromForm)
            $value = $GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"];
        elseif(is_array($arUserField))
            $value = $arUserField["SETTINGS"]["DEFAULT_VALUE"];
        else
            $value = "";
        if(($iblock_id > 0) && CModule::IncludeModule('iblock'))
        {
            $result .= '
            <tr valign="top">
                <td>'.GetMessage("USER_TYPE_IBSEC_DEFAULT_VALUE").':</td>
                <td>
                    <select name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]" size="5">
                        <option value="">'.GetMessage("IBLOCK_VALUE_ANY").'</option>
            ';

            $rsSections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$iblock_id));
            while($arSection = $rsSections->GetNext())
                $result .= '<option value="'.$arSection["ID"].'"'.($arSection["ID"]==$value? " selected": "").'>'.str_repeat("&nbsp;.&nbsp;", $arSection["DEPTH_LEVEL"]).$arSection["NAME"].'</option>';

            $result .= '</select>';
        }
        else
        {
            $result .= '
            <tr valign="top">
                <td>'.GetMessage("USER_TYPE_IBSEC_DEFAULT_VALUE").':</td>
                <td>
                    <input type="text" size="8" name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]" value="'.htmlspecialchars($value).'">
                </td>
            </tr>
            ';
        }

        return $result;

    }

    /**
     * Эта функция вызывается при выводе формы редактирования значения свойства.
     *
     * <p>Возвращает html для встраивания в ячейку таблицы.
     * в форму редактирования сущности (на вкладке "Доп. свойства")</p>
     * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
     * @param array $arUserField Массив описывающий поле.
     * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
     * @return string HTML для вывода.
     * @static
     */
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        printObj($arUserField);
        printObj($arHtmlControl);
        ob_start();
        // printObj($arUserField);
        if(strlen($arHtmlControl["VALUE"]) > 0)
            $resArr = unserialize(htmlspecialchars_decode($arHtmlControl["VALUE"]));

        $arUserField["SETTINGS"]["IBLOCK_ID"] = intval($arUserField["SETTINGS"]["IBLOCK_ID"]);

        $iblockID = $arUserField["SETTINGS"]["IBLOCK_ID"];
        if($iblockID > 0 && CModule::IncludeModule("iblock"))
        {
            // список клиентов
            $arFilter = array(
                "IBLOCK_ID" => $iblockID
            );
            $rsClients = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter);?>
            <?echo CMedialib::InputFile(
                $arHtmlControl["FIELD_NAME"]."[n".$i."]", 0,
                array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y",
                    "IMAGE_POPUP"=>"Y", "MAX_SIZE" => array("W" => 200, "H"=>200)), //info
                array("SIZE"=>1), //file
                array(), //server
                array(), //media lib
                    false, //descr
                array() //delete
            );?>

            Клиент<br/>
                <select name="EXT_PHOTO_CLIENT[]">
            <?while($arClient = $rsClients->GetNext()):?>
                <option value='".$arClient["ID"]."' <?=$resArr["CLIENT"] == $arClient["ID"] ? "selected='selected'" : ""?>><?=$arClient["NAME"]?></option>
            <?endwhile?>
            </select><br/>
            Название:<br/>
            <input type='text' name='EXT_PHOTO_NAME[]' value='<?=$resArr["NAME"]?>' />
            <br/><input type='button' name='add-ext-photo' value='Добавить' />
        <?}

        $return = ob_get_contents();
        ob_end_clean();
        return  $return;
    }

    /**
     * Эта функция вызывается при выводе значения свойства в списке элементов.
     *
     * <p>Возвращает html для встраивания в ячейку таблицы.</p>
     * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
     * @param array $arUserField Массив описывающий поле.
     * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
     * @return string HTML для вывода.
     * @static
     */
    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        if(strlen($arHtmlControl["VALUE"])>0)
        {
            $arValue = unserialize(htmlspecialchars_decode($arHtmlControl["VALUE"]));
            return $arValue["VALUE"]["VALUE"];
        }
        else
            return '&nbsp;';
    }

    /**
     * Эта функция валидатор.
     *
     * <p>Вызывается из метода CheckFields объекта $USER_FIELD_MANAGER.</p>
     * <p>Который в свою очередь может быть вызван из меторов Add/Update сущности владельца свойств.</p>
     * <p>Выполняется 2 проверки:</p>
     * <ul>
     * <li>на минимальную длину (если в настройках минимальная длина больше 0).
     * <li>на регулярное выражение (если задано в настройках).
     * </ul>
     * @param array $arUserField Массив описывающий поле.
     * @param array $value значение для проверки на валидность
     * @return array массив массивов ("id","text") ошибок.
     * @static
     */
    function CheckFields($arUserField, $value)
    {
        $aMsg = array();
        return $aMsg;
    }

    /**
     * Эта функция вызывается перед сохранением значений в БД.
     *
     * <p>Вызывается из метода Update объекта $USER_FIELD_MANAGER.</p>
     * <p>Для множественных значений функция вызывается несколько раз.</p>
     * @param array $arUserField Массив описывающий поле.
     * @param mixed $value Значение.
     * @return string значение для вставки в БД.
     * @static
     */

    /* function OnBeforeSave($arUserField, $value)
     {
         $bOk = true;
         foreach($value as $key=>$arValue)
         {
             if($key == "VALUE" && intval($arValue) <= 0)
                 $bOk = false;

             $resultAr[$key] = trim($arValue);
         }
        if($bOk)
        {
             $resultAr["VALUE"] = $resultAr;
             return serialize($resultAr);
        }
        else
        {
            return false;
        }
     }*/
}