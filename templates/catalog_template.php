<!-- HTML страницы -->
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <title> Список объявлений </title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link href="/css/styles.css" type="text/css" rel="stylesheet">
   </head>

   <body>
	<div id="ann_column">   
	   <div class="list_of_ann">
	   <? while ($row1 = $res1->fetch(PDO::FETCH_ASSOC)):?> 

		  <div class="ann_item">

			 <?if(isset($arImages[$row1["annNumber"]][0])):?>

			    <div class="photo-preview">
				   <a href="/<?=$section?>/detail/<?=$row1['annNumber']?>.html">
                                      <img class="ann_preview_photo" src="/<?=$imagePath?><?=$section?>/<?=substr($arImages[$row1['annNumber']][0],0,2)?>/<?=$arImages[$row1['annNumber']][0]?>" > </>
</a>
				</div>

			 <?endif;?>
			 
			 <div class="description">
				<div class="ann_title">
				   <a href="/<?=$section?>/detail/<?=$row1['annNumber']?>.html"><?=$row1["title"]?></a>
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

	   <?endwhile;?>
	   </div>

	   <div id="ann_paginator"><?include('include/paginator.php');?></div>
	</div>   
   </body>
</html>
