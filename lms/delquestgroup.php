<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";
  $id = $_POST["id"];

  $tg = mysqli_query($mysqli,"SELECT userid FROM questgroups WHERE id='".$id."' LIMIT 1;");
  $tgdata = mysqli_fetch_array($tg);

  if ((defined("IN_SUPERVISOR") and $tgdata['userid'] == USER_ID) or defined("IN_ADMIN")) 
  {
   // Удалим вопросы, если вдруг есть
   mysqli_query($mysqli,"START TRANSACTION;");
   $qst = mysqli_query($mysqli,"SELECT id FROM questions WHERE qgroupid='".$id."' ORDER BY id");
   while($member = mysqli_fetch_array($qst))
   {
        $query = "DELETE FROM answers WHERE questionid=".$member['id'];
        mysqli_query($mysqli,$query);
   }
   $query = "DELETE FROM questions WHERE qgroupid=".$id;
   mysqli_query($mysqli,$query);

   // Удалим группу
   $query = "DELETE FROM questgroups WHERE id=".$id;
   mysqli_query($mysqli,$query);
   mysqli_query($mysqli,"COMMIT");
   $json['ok'] = '1';  
  }
  else 
   $json['ok'] = '0';  
} 
else 
   $json['ok'] = '0';  
echo json_encode($json); 
