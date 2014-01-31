<?
include("include/init.php");
include("include/mysql_connect.php");
include("include/functions.php");

$sql = "SELECT * FROM `announcements_temp2`;";
$all = $db->query($sql) or die(mysql_error());
//$i=0;
while ($row = $all->fetch(PDO::FETCH_ASSOC)):
   //printAdmin($row);
//$i++;
   $sql3 = "INSERT INTO `announcements_temp` VALUES(
           '".$row['annNumber']."',
           '".$row['pageTitle']."',
           '".$row['pageDescription']."',
           '".$row['title']."',
           '".$row['price']."',
           '".$row['seller']."',
           '".$row['seller_type']."',
           '".$row['address']."',
           '".$row['itemParams']."',
           '".$row['itemDescription']."')";
   if(!($db->query($sql3))):
        continue;
   endif; 
  
endwhile;