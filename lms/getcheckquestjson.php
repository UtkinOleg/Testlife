<?php
if(defined("IN_SUPERVISOR") AND USER_EXPERT_KIM) {
 
  include "config.php";

  $questionid = intval($_POST['qid']);  
  $qgroupid = intval($_POST['qgid']);  
  $check = intval($_POST['c']);  
  $expertid = USER_ID;
  
  mysqli_query($mysqli,"START TRANSACTION;");
  
  $query = "INSERT INTO expertquestions VALUES (0,
  $expertid,
  $questionid,
  $check,
  $qgroupid,
  NOW())";
  
  mysqli_query($mysqli,$query);
  mysqli_query($mysqli,"COMMIT");

  if ($check==1)
   $json['ok'] = 'Y'; 
  else
  if ($check==0)
   $json['ok'] = 'N'; 
  
} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
