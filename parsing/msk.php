<? header("Content-Type: text/html; charset=utf-8");
//Парсинг ссылок на детальные страницы объявлений
include("include/init.php");
include("include/mysql_connect.php");
include("include/phpQuery-onefile.php");
include("include/functions.php");
$i=0;
$HTML = file_get_contents("http://msk.am.ru/all/search/?q=&qs=1");
$doc = phpQuery::newDocumentHTML($HTML);

while($urls[] = $doc[".info-inner:eq(".$i.") .title a"]->attr("href")):
   $i++;
endwhile;
array_pop($urls);

$sql = "INSERT INTO msk_urls(url) VALUES";

foreach($urls as $url):
   $sql .= "('".$url."'),";
endforeach;

$sql = substr($sql,0,-1);
$sql .=';';

$db->query($sql);
//printAdmin($sql);
echo '</br>Время выполнения скрипта: '.$exec_time = microtime(true) - $start_time .' секунд';