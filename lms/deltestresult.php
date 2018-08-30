<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $testid1 = $_GET["tid"];
  $begindate1 = $_GET["bdate"];
  $enddate1 = $_GET["edate"];
  $userid1 = $_GET["uid"];
  $order1 = $_GET["order"];
  
  $id = $_GET["id"];

  $query = "SELECT * FROM singleresult WHERE id='".$id."' LIMIT 1;";
  $res = mysqli_query($mysqli,$query);
  if ($res) 
   $result = mysqli_fetch_array($res);
  else 
   puterror("Ошибка при обращении к базе данных");
  $tid = $result['testid'];
  $userid = $result['userid'];
  $sign = $result['signature'];
  mysqli_free_result($res); 

  mysqli_query($mysqli,"START TRANSACTION;");
  
  $query = "DELETE FROM testresults WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."'";
  if(!mysqli_query($mysqli,$query))
   puterror("Ошибка при обращении к базе данных");

  // Удалим протокол
  $td = mysqli_query($mysqli,"SELECT * FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."' ORDER BY id;");
  while($testdata = mysqli_fetch_array($td))
  {
   $query = "DELETE FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(!mysqli_query($mysqli,$query))
    puterror("Ошибка при обращении к базе данных");
   $query = "DELETE FROM tmpshortanswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."'";
   if(!mysqli_query($mysqli,$query))
    puterror("Ошибка при обращении к базе данных");
  }
  mysqli_free_result($td); 

  $query = "DELETE FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."'";
  if(!mysqli_query($mysqli,$query))
   puterror("Ошибка при обращении к базе данных");


  $query = "DELETE FROM singleresult WHERE id='".$id."'";
  if(mysqli_query($mysqli,$query))
  {
    mysqli_query($mysqli,"COMMIT");
    echo '<script language="javascript">';
    echo 'location.replace("'.$site.'/viewtestresults&tid='.$testid1.'&bdate='.$begindate1.'&edate='.$enddate1.'&uid='.$userid1.'&order='.$order1.'&start='.$_GET['start'].'");';
    echo '</script>';
    exit();
  }
  else 
  {
   mysqli_query($mysqli,"COMMIT");
   puterror("Ошибка при обращении к базе данных");
  }
} 
else 
 die;  
?>