<?
class CParserUtil {
    public static function ArrayIconv($inArray) {
        $outArray = array();

        return $outArray;
    }
    public static  function StrIconv($str, $inCharset = "CP1251", $outCharset = "UTF-8") {
        return iconv($inCharset, $outCharset, $str);
    }
}