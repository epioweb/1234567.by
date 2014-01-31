<?
/*
header("Content-Type: text/html; charset=utf-8");
include("include/init.php");
include("include/mysql_connect.php");
include("include/phpQuery-onefile.php");
include("include/functions.php");

$sqlGlob = "SELECT url FROM urls LIMIT 60000,100;";

$allUrls = $db->query($sqlGlob) or die(mysql_error());
//$i=0;
while ($row = $allUrls->fetch(PDO::FETCH_ASSOC)):

INSERT INTO avito.urls(url) SELECT url FROM avito_temp.urls