<?
//header("Content-Type: text/html; charset=utf-8");
include("../include/init.php");
include("../include/mysql_connect.php");
include("../include/functions.php");

// получаем номер страницы из URL
if(isset($_GET['page'])):
   $pg = $_GET['page'];
else:
   $pg = 1;
endif;
$start = ($pg<1) ? 0 : ($pg-1)*$range; // получаем номер записи, с которой начнем выборку
$annTable="announcements_temp";
//Формирование строки номеров объявлений
//для вставки в запрос
$strIN='';
$sql = "SELECT annNumber FROM  ". $annTable ." ORDER BY annNumber DESC LIMIT ". $start .", ". $range .";";
$res = $db->query($sql) or die(mysql_error());
while ($row = $res->fetch(PDO::FETCH_ASSOC)): 
   $strIN .= $row["annNumber"].",";
endwhile;

$strIN = mb_substr($strIN, 0, -1);//Обрезка последней запятой
$sql1 = "SELECT * FROM  ". $annTable ." ORDER BY annNumber DESC LIMIT ". $start .", ". $range .";";
$res1 = $db->query($sql1) or die(mysql_error());

$sql2 = "SELECT * FROM  ". $annImagesTable ." WHERE annID IN (". $strIN .")";
$res2 = $db->query($sql2) or die(mysql_error());
while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)): 
   $arImages[$row2['annID']][] = $row2['imageName'];
endwhile;
?>
<!-- HTML страницы -->
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <title> Список объявлений </title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link href="css/styles.css" type="text/css" rel="stylesheet">
   </head>

   <body>
	<div id="ann_column">   
	   <div class="list_of_ann">
	   <? while ($row1 = $res1->fetch(PDO::FETCH_ASSOC)):?> 
		  <div class="ann_item">
			 <?if(isset($arImages[$row1["annNumber"]][0])):?>
			    <div class="photo-preview">
				   <a href="detail.php?annNumber=<?=$row1['annNumber']?>">
                                      <img class="ann_preview_photo" src='<?=$imagePath?><?=substr($arImages[$row1["annNumber"]][0],0,2)?>/<?=$arImages[$row1["annNumber"]][0]?>'/>
				   </a>
				</div>
			 <?endif;?>
			 
			 <div class="description">
				<div class="ann_title">
				   <a href="detail.php?annNumber=<?=$row1['annNumber']?>"><?=$row1["title"]?></a>
				</div>
				<div class="ann_price">
				  <span><b>Цена: </b></span><span><?=$row1["price"];?></span>
			   </div>
			   <div class="ann_seller_type">
				  <span><b>Продавец: </b></span><span><?=$row1["seller"]?></span> <span style="color:gray"><?=($row1["seller_type"] != "Не указан") ? $row1["seller_type"] : ''?></span>    
			   </div>
			   <div class="ann_address">
				  <?=$row1["address"];?>
			   </div>   
			 </div>
		  </div>
		  <?//echo str_replace('•', '', $row["title"]) . "</br>";?>
	   <?endwhile;?>
	   </div>
	   <div id="YMapsID"></div>
	   <div id="ann_paginator"><?include('include/paginator.php');?></div>
	</div>   
   </body>
</html>
