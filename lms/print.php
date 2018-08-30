<?php
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
  {
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  

  // Эксперт
  $expert = $_GET["expert"];
  $project = $_GET["project"];
  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);
  $ownerid=$projectarray['ownerid'];
  
  if ((defined("IN_SUPERVISOR") and $ownerid == USER_ID) or defined("IN_ADMIN")) 
  {


  if (empty($project))
  {
   $res2=mysql_query("SELECT * FROM users WHERE id='".$expert."' LIMIT 1");
   $r2 = mysql_fetch_array($res2);
   $expertfio = $r2['userfio'];
  }
  // Выводим шапку страницы
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?
echo "<title>Печать - ".$expertfio."</title>";
?>
<link rel="StyleSheet" type="text/css" href="css/admin.css">
</head>
<body leftmargin="0" marginheight="0" marginwidth="0" rightmargin="0" bottommargin="0" topmargin="0" >
<?

  if (empty($project))
   $lst = mysql_query("SELECT * FROM shablondb WHERE userid='".$expert."' ORDER BY memberid");
  else
   $lst = mysql_query("SELECT * FROM shablondb WHERE memberid='".$project."' ORDER BY memberid");
  if (!$lst) puterror("Ошибка при обращении к базе данных");

  echo "<p align='right'><b>Председатель Конкурсной комиссии: </b></p>";
  echo "<p align='right'><b>__________________________/________________/</b></p>";

  while($list = mysql_fetch_array($lst))
  {

    if (!empty($project))
    {
     $eres2=mysql_query("SELECT * FROM users WHERE id='".$list['userid']."' LIMIT 1");
     $er2 = mysql_fetch_array($eres2);
     $expertfio = $er2['userfio'];
    }

    $res1=mysql_query("SELECT * FROM projects WHERE id='".$list['memberid']."' LIMIT 1");
    $r1 = mysql_fetch_array($res1);
    if ($paid==$r1['proarrid']) {
    ?>
    <p align='center'>
    <table width="80%" border="0" cellpadding=1 cellspacing=0>
    <tr><td>
    <p align='center'><b>ЭКСПЕРТИЗА ПРОЕКТА</b></p>

    <p align='right'><b>Регистрационный номер проекта №: <? echo $list['memberid'] ?></b></p>
    <p align='right'><b>Дата создания проекта: <? echo data_convert ($r1['regdate'], 1, 0, 0) ?></b></p>
    <p></p><p></p>

    </td></tr>
    <tr><td>
    <p align='center'>
    <table width="100%" border="1" cellpadding=1 cellspacing=0>
    <?

    
    $total = 0;
    $res2=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$paid."' ORDER BY id");
    while($group = mysql_fetch_array($res2))
    { 
    echo"<tr><td width='100' align='center'><b>№</b></td>";
        echo"<td>";
            echo"<b>".$group['name']."</b></td><td width='100'><b>Баллы</b></td>";
    echo"</tr>";
    
    $subtotal = 0; 
    $res3=mysql_query("SELECT * FROM shablon WHERE groupid='".$group['id']."' AND proarrid='".$paid."' ORDER BY id");
    $i=0;
    while($param = mysql_fetch_array($res3))
    { 
      echo"<tr>";
      $query4=mysql_query("SELECT * FROM leafs WHERE shablonid='".$param['id']."' AND shablondbid='".$list['id']."'");
      $r4 = mysql_fetch_array($query4);
      echo"<td width='100' align='center'><b>".++$i."</b></td><td><b>".$param['name']."</b></td>";
      echo"<td width='100' align='center'><b>".$r4['ball']."</b></td>";
      echo"</tr>";
      $subtotal += $r4['ball']; 
     } 
     echo"<tr><td></td><td><b>Итого баллов по разделу:</b></td>";
     echo"<td width='100' align='center'><b>".$subtotal."</b></td>";
     echo"</tr>";
     $total += $subtotal; 
   
     }  
    echo"<tr><td></td><td><b>Итого баллов:</b></td>";
    echo"<td width='100' align='center'><b>".$total."</b></td>";
    echo"</tr>";

//    echo"<tr><td>".$list['info']."</td><td></td></tr>";
    echo "</table></p>";
    echo "</td></tr>";
    echo "<tr><td>";

    echo "<p></p><p></p><p align='left'><b>Эксперт: __________________________</b> ".$expertfio."</p>";
    echo "<p align='left'><b>Дата экспертизы:</b> ".data_convert ($list['puttime'], 1, 0, 0)."</p>";

    echo "</td></tr>";
    echo "</table></p>";
    echo "<br style='page-break-after: always'>";
   } 
  }
  
  
?>
<br><br></body></html>
<?
  }
  else die;
  } 
  else die;
?>

