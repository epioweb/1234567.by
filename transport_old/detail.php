<? if(!isset($_GET["annNumber"])):  header("Location: /"); endif; ?>
<?
include("include/init.php");
include("include/mysql_connect.php");
include("include/functions.php");
 
$sql1 = "SELECT * FROM ".$annTable." WHERE annNumber=". $_GET['annNumber'] .";";
$res1 = $db->query($sql1) or die(mysql_error());
$announce = $res1->fetch(PDO::FETCH_ASSOC);//mysql_fetch_assoc($res1); 
 
$sql2 = "SELECT * FROM  annImages WHERE annID=". $_GET['annNumber'] .";";
$res2 = $db->query($sql2) or die(mysql_error());
 
?>
 
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <title><?=$announce['pageTitle']?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta content="<?=$announce['pageDescription']?>" name="description" />
      <script src="http://api-maps.yandex.ru/1.1/index.xml?key=AIUdrFIBAAAAGE3nZgMAJejaNUiXlXkjwLX_8PWyBCl0cjsAAAAAAAAAAABQevUh_1QhTJpjLFFcmIlVfPSs-A==" type="text/javascript"></script>
      <script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
      <link href="css/styles.css" type="text/css" rel="stylesheet">
   
<script type="text/javascript">
    window.onload = function(){
    var geocoder = new ymaps.geocode(
    // Строка с адресом, который нужно геокодировать
    '<?=$announce["address"]?>',
    // требуемое количество результатов
    { results: 1 }
    );
 
      // После того, как поиск вернул результат, вызывается callback-функция
    geocoder.then(
    function (res) {
    // координаты объекта
    var coord = res.geoObjects.get(0).geometry.getCoordinates();
    var map = new ymaps.Map('YMapsID', {
    // Центр карты - координаты первого элемента
    center: coord,
    // Коэффициент масштабирования
    zoom: 7,
    // включаем масштабирование карты колесом
    behaviors: ['default', 'scrollZoom'],
    controls: ['mapTools']
    });
    // Добавление метки на карту
    map.geoObjects.add(res.geoObjects.get(0));
    // устанавливаем максимально возможный коэффициент масштабирования - 1
    map.zoomRange.get(coord).then(function(range){
    map.setCenter(coord, range[1] - 1)
    });
    // Добавление стандартного набора кнопок
    map.controls.add('mapTools')
    // Добавление кнопки изменения масштаба
    .add('zoomControl')
    // Добавление списка типов карты
    .add('typeSelector');
    }
    );
    }
</script>
       
   </head>
 
   <body>
   <div id="ann_detail_column">  
      <div class="ann_item">
         <div class="detail_title">
               <h1><?=$announce['title']?></h1>
            </div>
         <div class="ann_photos">
            <center>
         <? $i=0; while ($images = $res2->fetch(PDO::FETCH_ASSOC)/*mysql_fetch_assoc($res2)*/):?>
            <img class="ann_detail_photo <?=($i !=2) ? 'center' : ''?>" src='<?=$imagePath?><?=substr($images["imageName"],0,2)?>/<?=$images["imageName"]?>'/>
         <? $i++;
            if($i == 2): break; endif;
            endwhile; ?>
            </center>
         </div>
         <div>
            <div class="detail_about">
              <div class="ann_price">
                 <span><b>Цена: </b></span><span><?=$announce['price']?></span>
              </div>
              <div class="ann_seller_type">
                 <span><b>Продавец: </b></span><span> <?=$announce['seller']?></span>
                 <?if($announce['seller_type'] !="Не указан"):?>
                 <span style="color:gray"><?=$announce['seller_type']?></span>
                 <?endif;?>
              </div>
              <div class="ann_address">
                 <?=$announce['address']?>
              </div>  
            </div>
         <div id="YMapsID"></div>
         </div>
         
         <div class="detail_description">
            <?=$announce['itemParams']?>
         </div>
         <div class="detail_description">
            <?=$announce['itemDescription']?>
         </div>
      </div>  
       
     
 
     
     
     
     
      <div style="clear:both;">
      <?
      $sql3 = "SELECT * FROM ".$annTable." LIMIT 0,1000;";
      $res3= $db->query($sql3);// or die(mysql_error());
      while($anns = $res3->fetch(PDO::FETCH_ASSOC)):/*$anns = mysql_fetch_assoc($res3)*/
              $arAnns[] = $anns;
      endwhile;
      //printAdmin($arAnns);
      shuffle($arAnns);
   
      $sql4 = "SELECT * FROM  annImages WHERE annID IN(";
      $cn=0;
      foreach($arAnns as $key4 => $ann):
         $sql4 .=$ann['annNumber']."," ;
         $five_images[$ann['annNumber']]['title'] = $ann['title'];
         $five_images[$ann['annNumber']]['price'] = $ann['price'];
         $cn++;
         if($cn == 5): break; endif;
      endforeach;      
      
      $sql4 = substr($sql4,0,-1).");";
      $res4 = $db->query($sql4) or die(mysql_error());
      while($ann2= $res4->fetch(PDO::FETCH_ASSOC)/*mysql_fetch_assoc($res4)*/):
         $five_images[$ann2["annID"]]["images"][] = $ann2["imageName"];
      endwhile;
?>
     </div>
      <div style="margin: 0 5;">
         <div style="float:left;">Похожие объявления:</div>
         <div style="float:left;">
            <? $e=0; foreach($five_images as $keyAnnID => $image):?>
            <div style="width:90px; margin:0 10; float:left;">
               <a href="detail.php?annNumber=<?=$keyAnnID;?>">
                  <img style="height:90px !important; width:90px !important;" src="<?=$imagePath?><?=(isset($image["images"][0])) ? (substr($image["images"][0],0,2)."/".$image["images"][0]) : 'no_photo.jpg'?>"/>
               <div><?=$image["title"]?></div>
               </a>
               <div><?=$image["price"]?></div>
            </div>
         <? $e++; endforeach;?>
         </div>
      </div>
   </div>  
 
   </body>
</html>