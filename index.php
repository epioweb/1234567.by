<?
header("Content-Type: text/html; charset=utf-8");
include("include/top-cache.php");
//phpinfo();
include("include/init.php");
include("include/mysql_connect.php");
include("include/functions.php");

	if(!isset($_REQUEST['section']) && !isset($_REQUEST['page'])):
	   $section="house";
	   include("templates/index_template.php");
	else:
		// получаем номер страницы из URL
		if(!isset($_REQUEST['section'])):
		   $section="house";
		else:
		   $section=$_REQUEST['section'];
		endif;

		if(isset($_REQUEST['page'])):
		   $pg = $_REQUEST['page'];
		else:
		   $pg = 1;
		endif;

		$start = ($pg<1) ? 0 : ($pg-1)*$range; // получаем номер записи, с которой начнем выборку
		//$annTable="announcements";
		//Формирование строки номеров объявлений
		//для вставки в запрос

		$strIN='';
		$sql = "SELECT annNumber FROM  ".$section."_". $annTable ." ORDER BY annNumber DESC LIMIT ". $start .", ". $range .";";

		$res = $db->query($sql) or die(mysql_error());
		while ($row = $res->fetch(PDO::FETCH_ASSOC)): 
		   $strIN .= $row["annNumber"].",";
		endwhile;

		$strIN = mb_substr($strIN, 0, -1);//Обрезка последней запятой
		$sql1 = "SELECT * FROM  ".$section."_". $annTable ." ORDER BY annNumber DESC LIMIT ". $start .", ". $range .";";
		$res1 = $db->query($sql1) or die(mysql_error());

		$sql2 = "SELECT * FROM  ".$section."_". $annImagesTable ." WHERE annID IN (". $strIN .");";
		$res2 = $db->query($sql2) or die(mysql_error());
		while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)): 
		   $arImages[$row2['annID']][] = $row2['imageName'];
		endwhile;

		include("templates/catalog_template.php");
	endif;

include("include/bottom-cache.php");
?>