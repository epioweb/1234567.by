<?
include("include/init.php");
include("include/mysql_connect.php");
include("include/functions.php");

$sql = "SELECT `annNumber` FROM `announcements` LIMIT 5000, 3000;";
$allNumbers = $db->query($sql) or die(mysql_error());
//$i=0;
while ($row = $allNumbers->fetch(PDO::FETCH_ASSOC)):

   $sql2 = "SELECT count(*) FROM `announcements` WHERE annNumber=".$row['annNumber'].";";
  
   $annObj = $db->query($sql2) or die(mysql_error());
   $ann = $annObj->fetch(PDO::FETCH_ASSOC);
   
   if($ann["count(*)"] !=1):
       $sql3 = "DELETE FROM `announcements` WHERE annNumber=".$row['annNumber']." LIMIT ".($ann["count(*)"]-1).";";
       $db->query($sql3);
   endif;
   //$i++;
   
    
   //printAdmin($ann["count(*)"]);
   /*
   $sql3 = "INSERT INTO `announcements_temp` VALUES(
           '".$ann['annNumber']."',
           '".$ann['pageTitle']."',
           '".$ann['pageDescription']."',
           '".$ann['title']."',
           '".$ann['price']."',
           '".$ann['seller']."',
           '".$ann['seller_type']."',
           '".$ann['address']."',
           '".$ann['itemParams']."',
           '".$ann['itemDescription']."')";
   //echo "</br></br></br>"; 
   if(!($db->query($sql3))):
        continue;
   endif; 
*/
endwhile;


echo '</br>Время выполнения скрипта: '.$exec_time = microtime(true) - $start_time .' секунд';