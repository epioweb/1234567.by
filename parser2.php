<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
<? header("Content-Type: text/html; charset=utf-8");
include("../include/init.php");
include("../include/mysql_connect.php");
include("../include/phpQuery-onefile.php");
include("../include/functions.php");

$section="house";//раздел, который парсим

//Get all annNumbers from the database
$sqlGlob = "SELECT url FROM ".$section."_urls WHERE PARSED='N' LIMIT 9000;";
$allUrls = $db->query($sqlGlob) or die(mysql_error());
while ($row = $allUrls->fetch(PDO::FETCH_ASSOC)):
      $HTML2 = file_get_contents("http://www.avito.ru". $row["url"]);
      $doc2 = phpQuery::newDocumentHTML($HTML2);
      $annNumber = $doc2["#item_id"]->text();

      $data[$annNumber]["annNumber"] = $annNumber;
      $data[$annNumber]["pageTitle"] = str_replace("— Бесплатные объявления на сайте AVITO.ru" , "",$doc2['title']->text());
  
      //Выпиливание из меты названий разделов
      $data[$annNumber]["pageDescription"] = $doc2['meta[name="description"]']->attr( "content" );
      foreach($arSubSections as $subSect):
         $data[$annNumber]["pageDescription"] = str_replace("в разделе ".$subSect." бесплатной доски объявлений сайта AVITO.ru", "",$data[$annNumber]["pageDescription"]);
      endforeach;

      $data[$annNumber]["title"] = $doc2["div.item h1.item_title"] ->text();
      $data[$annNumber]["price"] = $doc2[".t-item-price strong"]->text();
      $data[$annNumber]["seller"] = $doc2["#seller strong"]->text();
 
      if($doc2["#seller .c-2"]->text()):
         $data[$annNumber]["seller_type"] = $doc2["#seller .c-2"]->text();
      else:
         $data[$annNumber]["seller_type"] = "Не указан";
      endif;
 
      $data[$annNumber]["address"] = $doc2["#map .c-2"]->text();
      $data[$annNumber]["itemParams"] = $doc2[".item-params.c-1"]->text();
      $data[$annNumber]["itemDescription"] = $doc2["#desc_text"]->html();
 
      //a getting of images urls and a saving of images on HD        
      if ($doc2[".j-zoom-gallery-img"]->attr("src")):
         $imageUrl[$annNumber][0] = "http:".$doc2[".j-zoom-gallery-img"]->attr("src");
         $imageName[$annNumber][0] = array_pop(explode('/', $imageUrl[$annNumber][0])); 
      endif;
      if ($doc2[".gallery-wrapper .ll.fit a:eq(1)"]->attr("href")):
         $imageUrl[$annNumber][1] = "http:".$doc2[".gallery-wrapper .ll.fit a:eq(1)"]->attr("href");
         $imageName[$annNumber][1] = array_pop(explode('/', $imageUrl[$annNumber][1])); 
      endif;
      
      //запись инфы об объявлении
      $sql1 = "INSERT INTO ".$section."_".$annTable."(
                        annNumber,
                        pageTitle, 
                        pageDescription, 
                        title, 
                        price, 
                        seller, 
                        seller_type, 
                        address, 
                        itemParams, 
                        itemDescription) 
             VALUES(
                        '".trim($data[$annNumber]["annNumber"])."',
                        '".trim($data[$annNumber]["pageTitle"])."',
                        '".trim($data[$annNumber]["pageDescription"])."',
                        '".trim($data[$annNumber]["title"])."',
                        '".trim($data[$annNumber]["price"])."',
                        '".trim($data[$annNumber]["seller"])."',
                        '".trim($data[$annNumber]["seller_type"])."',
                        '".trim($data[$annNumber]["address"])."',
                        '".trim($data[$annNumber]["itemParams"])."',
                        '".trim($data[$annNumber]["itemDescription"])."');";
            $resAddAnn=$db->query($sql1);
	    $db->query("UPDATE ".$section."_urls SET PARSED='Y' WHERE url='".$row["url"]."';");//set a flag PARSED
  
      if($resAddAnn):
         $sql2 = "INSERT INTO ".$section."_".$annImagesTable."(
                           annID,
                           imageName) VALUES";
         //Запись картинок
         if($data[$annNumber]):
            foreach($imageUrl[$annNumber] as $key1 => $url):
               $imageN = array_pop(explode('/', $url));
               file_put_contents($imagePath . $section ."/". substr($imageN,0,2) ."/". array_pop(explode('/', $url)) , file_get_contents($url));
               unset($imageN);
               $sql2 .="(
                       '" . $annNumber . "',
                       '" . $imageName[$annNumber][$key1] . "'),";
            endforeach;
            $sql2 = substr($sql2,0,-1);
            $sql2 .= ";";
            $db->query($sql2);//Запись в БД инфы о картинках оюъявления
         endif;
      endif;
      
endwhile;

echo '</br>Время выполнения скрипта: '.$exec_time = microtime(true) - $start_time .' секунд';
