<?
$host='localhost'; // имя хоста (уточняется у провайдера)
$database='avito';
$user='root';
$pswd='BaV0960903';
//$database='alexb';
//$user='alexbadmin';
//$pswd='FDtys43dDss';
$annTable='announcements';
$annImagesTable='annImages';

try 
{
   $db = new PDO("mysql:host=$host;dbname=$database", $user, $pswd);
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
$db->query("SET character_set_client='utf8'");
$db->query("SET character_set_connection='utf8'");
$db->query("SET character_set_results='utf8'");
$db->query("SET NAMES 'utf8'");
