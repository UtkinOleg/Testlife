<?php
if(defined("USER_REGISTERED")) 
{  
  include "config.php";
  $token = $_POST['s']; 

  mysqli_query($mysqli,"START TRANSACTION;");
  if(!mysqli_query($mysqli,"UPDATE msgs SET readed = '1' WHERE touser='".USER_ID."' AND signature='".$token."'"))
    $json['ok'] = '0'; 
  mysqli_query($mysqli,"COMMIT;");
  
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed=0 LIMIT 1;");
  $param = mysqli_fetch_array($sql); 
  $json['count'] = $param['count(*)']; 
  mysqli_free_result($sql);

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM msgs WHERE touser='".USER_ID."' AND signature='".$token."'");
  $param = mysqli_fetch_array($sql); 
  $json['content'] = $param['body']; 
  mysqli_free_result($sql);
  $json['ok'] = '1';  
} else 
   $json['ok'] = '0'; 
echo json_encode($json);
?>
