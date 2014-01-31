<?
ini_set('max_execution_time', 600);

function curl_get($host, $referer = null){
    $ch = curl_init();
	
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  
    $html = curl_exec($ch);
    echo curl_error($ch);
    curl_close($ch);
    return $html;
}

$result = curl_get("avito.ru" , 'http://google.com');
echo $result;