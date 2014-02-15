<?

/**
 * Сообщение уведомления
 *
 * Выводит сообщение в виде
 * <div class="b-info-msg"><span>'.$mess.'</span></div>
 *
 * @param $mess Текст сообщения
 * @param $style Дополнительный стиль
 *
 * @return string
 */
function ShowStyledNotice($mess, $style="")
{
    return '<div class="b-info-msg alert alert-success" style="'.$style.' "><span>'.$mess.'</span></div>';
}

/**
 * Сообщение об ошибке. Выводит сообщение в виде
 *  <div class="b-error-msg"><span>'.$mess.'</span></div>
 *
 * @param $mess Текст сообщения
 * @param $style Дополнительный стиль
 * @return string
 */
function ShowStyledError($mess, $style="")
{
    return '<div class="b-error-msg alert alert-error" style="'.$style.' "><span>'.$mess.'</span></div>';
}

/**
 * Функция контроля нечетности
 *
 * @var string $var
 * @return bool
 */
function odd($var)
{
    return($var & 1);
}

/**
 * Функция контроля четности
 *
 * @var string $var
 * @return bool
 */
function even($var)
{
    return(!($var & 1));
}

/**
 * Форматированный массив
 * @var array $obj Массив или объект для вывода
 * @return none
 */
function printObj($obj) {
    global $USER;
    if($USER->IsAdmin())
    {
        echo '<pre>'; print_r($obj); echo '</pre>';
    }
}

/**
 * Форматированный массив. Клон функции printObj
 * @var array $obj Массив или объект для вывода
 * @return none
 */
function prent($obj) {
    echo '<pre>'; print_r($obj); echo '</pre>';
}

/**
 * Скрытый форматированный массив
 * @var array $obj Массив или объект для вывода
 * @return none
 */
function printHiddenObj($obj) {
    echo '<pre style="display:none;">'; print_r($obj); echo '</pre>';
}

/**
 * Скрытый форматированный массив
 * @var array $obj Массив или объект для вывода
 * @return none
 */
function print_admin($obj) {
    global $USER;
    $id=$USER->GetID();
    if ($USER->IsAdmin() && $id==7497)
        echo '<pre>'; print_r($obj); echo '</pre>';
}

/**
 * Скрытый форматированный массив. Клон фукнции printHiddenObj
 * @var array $obj Массив или объект для вывода
 * @return none
 */
function prentH($obj) {
    echo '<pre style="display:none;">'; print_r($obj); echo '</pre>';
}

/**
 * Добавляет запись в лог файл
 * Работает на основе AddMessage2Log
 * @var mixed $message Сообщение
 * @return none
 */
function add2Log($message) {
    ob_start();
    print_r($message);
    AddMessage2Log(ob_get_clean());
    ob_end_clean();
}

/**
 * Функция человекоподобных окончаний
 * @var float $valueToShow Число для определения
 * @return string
 */
function numEnding($valueToShow)
{
    $simple = array("2","3","4");

    $voutCountEnd =  substr($valueToShow, -1);
    if ((strlen($valueToShow) > 1) && (substr($valueToShow, -2,1) == "1"))
        return "MORE";
    else
    {
        if (in_array($voutCountEnd, $simple))
            return "TWO";
        elseif ($voutCountEnd == "1")
            return "ONE";
        else
            return "MORE";
    }
}

/**
 * Функция возвращает массив смасштабированных изображений
 * по заданным параметрам. Использует функция resizer()
 *
 * @var array $arSorceImg - массив, описывающий исходное изображение
 * @var array $arSmallParams - максимальная высота и ширина малых изображений
 * @var array $arMediumParams - максимальная высота и ширина средних изображений
 * @var array $arBigParams - максимальная высота и ширина больших изображений
 *
 * @return array $arResult.
 * Возвращает массив вида
 *      array(
 *          "PREVIEW_PICTURE"=>array(
 *              "WIDTH"=>xxx,
 *              "HEIGHT"=>xxx,
 *              "SRC"   => '/'
 *          ),
 *          "MIDDLE_PICTURE"=>array(
 *              "WIDTH"=>xxx,
 *              "HEIGHT"=>xxx,
 *              "SRC"   => '/'
 *          ),
 *          "DETAIL_PICTURE"=>array(
 *              "WIDTH"=>xxx,
 *              "HEIGHT"=>xxx,
 *              "SRC"   => '/'
 *          )
 *      )
 */
function GetResizedImages($arSorceImg, $arSmallParams, $arMediumParams, $arBigParams, $trim=true, $needWatermark = false)
{
    if(!is_array($arSorceImg))
        return false;

    if($trim)
        $mode = "trim";
    else
        $mode = "in";

    $arResult  = false;

    // малое изображение
    if(is_array($arSmallParams))
    {
        //IMAGES_LIST_MAX_HEIGHT
        $arImageParams = array(
            "WIDTH" => $arSmallParams["WIDTH"],
            "HEIGHT"    => $arSmallParams["HEIGHT"],
            "SRC"   => CWsImageTools::Resizer("/thumb/".$arSmallParams["WIDTH"]."x".$arSmallParams["HEIGHT"]."x".$mode.$arSorceImg["SRC"], false)
        );
        $arResult["PREVIEW_PICTURE"] = $arImageParams;
    }

    // среднее изображение
    if(is_array($arMediumParams))
    {
        //IMAGES_LIST_MAX_HEIGHT
        $arImageParams = array(
            "WIDTH" => $arMediumParams["WIDTH"],
            "HEIGHT"    => $arMediumParams['HEIGHT'],
            "SRC"   => CWsImageTools::Resizer("/thumb/".$arMediumParams["WIDTH"]."x".$arMediumParams['HEIGHT']."x".$mode.$arSorceImg["SRC"], $needWatermark)
        );
        $arResult["MIDDLE_PICTURE"] = $arImageParams;
    }

    // максимально здоровое изображение
    $bResize = false;

    if($arSorceImg["HEIGHT"] > $arBigParams["HEIGHT"]
        || $arSorceImg["WIDTH"] > $arBigParams["WIDTH"])
    {
        $maxHeight = $arBigParams["HEIGHT"];
        $maxWidth = $arBigParams["WIDTH"];
        $bResize = true;
    }
    elseif($arSorceImg["HEIGHT"] > $arSorceImg["WIDTH"])
    {
        $maxHeight = $arSorceImg["HEIGHT"];
        $maxWidth = $arSorceImg["HEIGHT"];

        $bResize = true;
    }
    elseif ($arSorceImg["HEIGHT"] < $arSorceImg["WIDTH"])
    {
        $maxHeight = $arSorceImg["WIDTH"];
        $maxWidth = $arSorceImg["WIDTH"];
        $bResize = true;
    } else {
        $maxWidth = $arBigParams["WIDTH"];
        $maxHeight = $arBigParams["HEIGHT"];
    }

    //if($bResize)
    //{
        $arImageParams = array(
            "WIDTH" => $maxWidth,
            "HEIGHT"    => $maxHeight,
            "SRC"   => CWsImageTools::Resizer("/thumb/".$maxWidth."x".$maxHeight."x".$mode.$arSorceImg["SRC"], $needWatermark)
        );
        $arResult["DETAIL_PICTURE"] = $arImageParams;
        unset($bResize);
    //}
    return $arResult;
}


/**
 * Проверка статуса консультанта (on-line и off-line)
 *
 * @return bool
 */
function checkWebimOnline()
{
    global $DB;
    // тащим параметр времени бездействия консультанта
    $strSql = "select vckey,vcvalue from chatconfig where vckey = 'online_timeout'";
    $resSettings = $DB->Query($strSql, false, $err_mess.__LINE__);
    if($arSetting = $resSettings->Fetch())
    {
        $strSql = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from chatoperator";
        $res = $DB->Query($strSql, false, $err_mess.__LINE__);
        if($arRes = $res->Fetch())
        {
            if($arRes['time'] < $arSetting['vcvalue'] && $arRes['total'] > 0)
                return true;
        }
    }
    return false;
}

/**
 * Функция очистки массива от лишних ключей
 *
 * @param $ar array - Входной массив
 * @param $arSelect array - Массив, который нужен на выходе
 *
 * @return array
 */
function ClearArr($ar, $arSelect)
{
    $tmpArr = array();
    if(is_array($arSelect) && is_array($ar))
    {
        foreach($arSelect as $key)
            $tmpArr[$key] = $ar[$key];
        $ar = $tmpArr;
    }

    return $ar;
}

/**
 * Функция очистки массива от значений с тильдой
 *
 * @param $ar array - Входной массив
 *
 * @return array
 */
function ClearArrTilda($ar)
{
    $tmpArr = array();
    if(is_array($ar))
    {
        foreach($ar as $key => $v) {
            if(stripos($key, "~") === false)
                $tmpArr[$key] = $ar[$key];
        }
        $ar = $tmpArr;
    }

    return $ar;
}

/**
 * Подветка фрагмента текста в строке
 * @param $text
 * @param $search
 * @return mixed
 */
function TextHighlight($text, $search)
{
    $haystack = preg_replace("/($search)/i","<span style='font-weight:bold'>\${1}</span>", $text);
    echo $haystack;
    return $haystack;
}

/**
 * Выделение подстроки в строке
 * @param $s string подстрока
 * @param $phrase string фраза
 *
 * @return string
 */
function MatchHighlight($s, $phrase)
{
    $s = trim($s);
    $sLength = strlen($s);

    if($sLength > 0 && stripos($phrase, $s) !== false)
    {
        // определить начальную позицию $s
        $startPos = stripos($phrase, $s);
        // определить конечную позицию $s
        $endPos = $startPos + $sLength;

        // вырезать строку исходя из позиции
        $searchStr = substr($phrase, $startPos, $sLength);
        $strLeft = substr($phrase, 0, $startPos);
        $strRight = substr($phrase, $endPos);

        // добавить жирность
        $searchStr = "<span style='font-weight:bold'>".$searchStr."</span>";
        // собрать фразу
        $phrase = $strLeft.$searchStr.$strRight;
    }

    return $phrase;
}