<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
   include "config.php";
   $id = $_POST["id"];

   $tg = mysqli_query($mysqli,"SELECT userid FROM folders WHERE id='".$id."' LIMIT 1;");
   $tgdata = mysqli_fetch_array($tg);

   if ((defined("IN_SUPERVISOR") and $tgdata['userid'] == USER_ID) or defined("IN_ADMIN")) 
   {
    mysqli_query($mysqli,"START TRANSACTION;");
    $query = "DELETE FROM folders WHERE id=".$id;
    mysqli_query($mysqli, $query);
    mysqli_query($mysqli,"COMMIT;");
    $json['ok'] = '1';  
   } else
   $json['ok'] = '0';  
} else 
   $json['ok'] = '0';  
echo json_encode($json); 
