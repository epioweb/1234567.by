<? header("Content-Type: text/html; charset=utf-8");
include("../include/init.php");
include("../include/mysql_connect.php");
include("../include/phpQuery-onefile.php");
include("../include/functions.php");

$section="transport";//раздел, который парсим


$j=1;
while($HTML = file_get_contents('http://www.avito.ru/moskva/transport?p='.$j.'&view=list')):
//while($HTML = file_get_contents('http://www.avito.ru/moskva/doma_dachi_kottedzhi?p='.$j.'&view=list')):
   
//получаем страницу со списком объявлений
   $doc = phpQuery::newDocumentHTML($HTML);
   
   $i=0;
   //Получаем ссылки на детальные страницы
   while($doc[".b-catalog-list .item:eq(".$i.") .title .h3 a"]->attr("href")):
      $url = $doc[".b-catalog-list .item:eq(".$i.") .title .h3 a"]->attr("href");
      $sql = "INSERT INTO ".$section."_urls(url) VALUES ('".$url."');";
      $db->query($sql);
      //unset($url);
      $i++;
   endwhile;
   $j++;
   sleep(2);
   if($j == 10): break; endif;
endwhile;

echo '</br>Время выполнения скрипта: '.$exec_time = microtime(true) - $start_time .' секунд';
