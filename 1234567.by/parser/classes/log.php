<?
/**
 * Лог процесса выполнения
 * Class CLog
 */
class CLog {
    static public function Log($str, $level = 1) {
        echo date("H:i:s", time()).str_repeat("......", $level).$str."<br/>";
    }
}