<?
$count = $db->query("SELECT * FROM ".$section."_". $annTable .";"); // выбираем все записи из таблицы 
$cnt = $count->rowCount(); // выбрав, узнаем сколько их штук всего
$pages = ceil($cnt/$range);//Получаем количество страниц

// Сам пагинатор, выводим номера страниц.
if($pg != 1):
   $url = "/".$section."/page/1/";
   echo $link = "<a href=".$url."> Начало </a> ... ";
endif;

if($pg > 1):
   $url = "/".$section."/page/".($pg-1)."/";
   echo $link = "<a href=".$url."> << </a> ";
endif;

//Вывод страниц слева от текущей
for($i=$pg-$addPages; $i<$pg; $i++):
   if($i >0):
      $url = $url = "/".$section."/page/".$i."/";
      echo $link = "<a href=".$url.">".$i."</a> ";
   endif;
endfor;

$url = $url = "/".$section."/page/".$pg;
echo $link = "<b>".$pg." </b>";

for($i=$pg+1; $i <= $pg+$addPages; $i++):
   if($i <= $pages):
      $url = $url = "/".$section."/page/".$i."/";
      echo $link = "<a href=".$url.">".$i."</a> ";
   endif;
endfor;

if($pg < $pages):
   $url = $url = "/".$section."/page/".($pg+1)."/";
   echo $link = "<a href=".$url."> >> </a> ";
endif;

if($pg != $pages):
   $url = $url = "/".$section."/page/".$pages."/";
   echo $link = " ... <a href=".$url."> Конец </a> ";
endif;
