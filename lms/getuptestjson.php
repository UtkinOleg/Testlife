<?php
     include "config.php";
     $sign = $_POST['s'];  

     $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT upcnt FROM testgroups WHERE signature='".$sign."' LIMIT 1;");
     $quest = mysqli_fetch_array($sql);
     $upcnt = $quest['upcnt'];
     mysqli_free_result($sql);

     $json['cnt'] = ++$upcnt; 
     if (mysqli_query($mysqli,"UPDATE testgroups SET upcnt=".$upcnt." WHERE signature='".$sign."'"))
      $json['ok'] = '1'; 
     else 
      $json['ok'] = '0'; 
  
     echo json_encode($json); 

