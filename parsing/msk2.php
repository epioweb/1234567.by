<? header("Content-Type: text/html; charset=utf-8");
//Парсинг ссылок на детальные страницы объявлений
include("include/init.php");
include("include/mysql_connect.php");
include("include/phpQuery-onefile.php");
include("include/functions.php");
$i=0;
$HTML = file_get_contents("http://msk.am.ru/used/land_rover/range_rover_evoque/avs-dicars--6dd4eb56/#snp3");
$doc = phpQuery::newDocumentHTML($HTML);

$temp = explode('|',$doc["title"]->text());
$data["pageTitle"] = $temp[0];

$temp = explode('|',$doc['meta[name="description"]']->attr( "content" ));
$data["pageDescription"] = $temp[0];

$data["title"] = $doc[".au-header-block h1"]->text();
echo  $data["price"] = $doc[".b-card-price__price"]->text();


$i++;
