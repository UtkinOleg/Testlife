<?php
if(defined("IN_ADMIN")) {  

  include "config.php";

  $id = $_POST["id"];

  $query = "SELECT * FROM singleresult WHERE signature='".$id."' LIMIT 1;";
  $res = mysqli_query($mysqli,$query);
  if ($res) $result = mysqli_fetch_array($res);
  $id = $result["id"];
  $tid = $result['testid'];
  $userid = $result['userid'];
  $sign = $result['signature'];
  mysqli_free_result($res); 

  mysqli_query($mysqli,"START TRANSACTION;");
  
  $query = "DELETE FROM testresults WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."'";
  if(mysqli_query($mysqli,$query))
   $json['ok'] = '1';  
  else 
   $json['ok'] = '0';  

  // Удалим протокол
  $td = mysqli_query($mysqli,"SELECT * FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."' ORDER BY id;");
  while($testdata = mysqli_fetch_array($td))
  {
   $query = "DELETE FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
   else 
    $json['ok'] = '0';  
   $query = "DELETE FROM tmpshortanswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
   else 
    $json['ok'] = '0';  
   $query = "DELETE FROM tmpsequence WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
   else 
    $json['ok'] = '0';  
   $query = "DELETE FROM tmpaccord1 WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
   else 
    $json['ok'] = '0';  
   $query = "DELETE FROM tmpaccord2 WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
   else 
    $json['ok'] = '0';  
  }
  mysqli_free_result($td); 

  $query = "DELETE FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."'";
  if(mysqli_query($mysqli,$query))
    $json['ok'] = '1';  
  else 
    $json['ok'] = '0';  


  $query = "DELETE FROM singleresult WHERE id='".$id."'";
  if(mysqli_query($mysqli,$query))
   $json['ok'] = '1';  
  else 
   $json['ok'] = '0';  
  mysqli_query($mysqli,"COMMIT");

} 
else 
 $json['ok'] = '0';  

echo json_encode($json); 