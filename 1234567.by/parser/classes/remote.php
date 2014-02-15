<?
class CRemote {
    private $url;
    function __construct($url) {
        $this->url = $url;
    }

    function GetContents() {
        //CLog::Log("Обработка страницы ", 1);
        if ($stream = fopen($this->url, 'r')) {
            // вывести всю страницу начиная со смещения 10
            $contents = stream_get_contents($stream);

            fclose($stream);
            return $contents;
        } else {
            CLog::Log("Ошибка подключения к серверу");
        }
    }
}