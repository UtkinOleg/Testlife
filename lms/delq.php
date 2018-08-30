<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";

  $id = $_POST["id"];
  $grid = $_POST["grid"];

  $tg = mysqli_query($mysqli,"SELECT userid FROM questgroups WHERE id='".$grid."' LIMIT 1;");
  $tgdata = mysqli_fetch_array($tg);

  if ((defined("IN_SUPERVISOR") and $tgdata['userid'] == USER_ID) or defined("IN_ADMIN")) 
  {
   mysqli_query($mysqli,"START TRANSACTION;");
   $sql = mysqli_query($mysqli,"SELECT id FROM questions WHERE id='".$id."' ORDER BY id");
   while($member = mysqli_fetch_array($sql))
   {
     $query = "DELETE FROM answers WHERE questionid=".$member['id'];
     mysqli_query($mysqli,$query);
   }
   mysqli_free_result($sql);
   $query = "DELETE FROM questions WHERE id=".$id;
   mysqli_query($mysqli,$query);
   mysqli_query($mysqli,"COMMIT;");
   $json['ok'] = '1';  
 }
 else 
  $json['ok'] = '0';  
} 
else 
 $json['ok'] = '0';  

echo json_encode($json); 
