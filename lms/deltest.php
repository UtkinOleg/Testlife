<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
   include "config.php";
   $id = $_POST["id"];

   $tg = mysqli_query($mysqli,"SELECT ownerid FROM testgroups WHERE id='".$id."' LIMIT 1;");
   $tgdata = mysqli_fetch_array($tg);

   if ((defined("IN_SUPERVISOR") and $tgdata['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
    mysqli_query($mysqli,"START TRANSACTION;");
    
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM psymode WHERE testid='".$id."' ORDER BY id;");
    while($psy = mysqli_fetch_array($sql))
     mysqli_query($mysqli,"DELETE FROM psyquestions WHERE psyid=".$psy['id']);
    mysqli_free_result($sql); 
    
    mysqli_query($mysqli,"DELETE FROM psymode WHERE testid=".$id);
    mysqli_query($mysqli,"DELETE FROM usergrp WHERE testid=".$id);
    mysqli_query($mysqli,"DELETE FROM testdata WHERE testid=".$id);
    mysqli_query($mysqli,"DELETE FROM testgroups WHERE id=".$id);
    
    mysqli_query($mysqli,"COMMIT;");
    $json['ok'] = '1';  
   } else
   $json['ok'] = '0';  
} else 
   $json['ok'] = '0';  
echo json_encode($json); 
