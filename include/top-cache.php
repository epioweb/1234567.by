<?php
if(isset($_REQUEST['section'])):
   $sect="_".$_REQUEST['section'];
else:
   $sect="";
endif;
if(isset($_REQUEST['page'])):
   $numpage="_".$_REQUEST['page'];
else:
   $numpage="";
endif;

$url = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $url);
$file = $break[count($break) - 1];
$cachefile = 'cache/'.'cached-'.substr_replace($file ,"",-4).$sect.$numpage.'.html';
$cachetime = 18000;

// Обслуживается из файла кеша, если время запроса меньше $cachetime
if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    include($cachefile);
    exit;
}
ob_start(); // Запуск буфера вывода
?>

