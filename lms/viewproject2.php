<?php
  if(defined("IN_ADMIN")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $pid = $_GET["id"];

  $title=$titlepage="Просмотр проекта №".$pid;
  $helppage='';

  // Выводим шапку страницы
  include "topadmin3.php";
  
  if(defined("IN_ADMIN") or defined("IN_EXPERT") or defined("IN_SUPERVISOR"))
   $gst = mysql_query("SELECT * FROM projects WHERE id='".$pid."'");
  else
   $gst = mysql_query("SELECT * FROM projects WHERE id='".$pid."' AND userid='".USER_ID."'");

  if (!$gst) puterror("Ошибка при обращении к базе данных");
  
  $member = mysql_fetch_array($gst);
  
  if (!empty($member)) 
  {

  $tableheader = "class=tableheaderhide";
  echo "<p><div id='menu_glide' class='menu_glide'>
        <table width='800' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  echo "<tr><td><p align='center'>".$member['info']."</p></td></tr>";   


  $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  while($param = mysql_fetch_array($res3))
   { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);

    if ($param['content']=="yes") {
     echo"<tr>";
     echo"<td class='tableheader'><p class=help>".$param['name']."</p></td>";
     echo"</tr>";
     echo"<tr><td>".$param4['content']."</td></tr>";
    }
    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) {
      echo"<tr>";
      $kb = round($param4['filesize']/1024,2);
      echo "<td><a href='file.php?id=".$param4['secure']."' target='_blank' title='Загрузить прикрепленный файл'>".$param4['filename']."</a>&nbsp;(".$kb."&nbsp;кб.)</td></tr>";
     }
    }
   } 

  echo "<tr class='tableheader'><td><p class=help>Дата создания проекта</p></td></tr>";   
  echo "<tr></td><td><p class=zag2>".data_convert ($member['regdate'], 1, 0, 0)."</p></td></tr>";

  echo "<tr class='tableheader'><td><p class=help>Статус проекта</p></td></tr>";   
  if ($member['status']=='created') 
    {
     echo "<tr><td width='100'><p class=zag2>Создание и изменение</p>
     <p>Проект находится в процессе создания и изменения. 
     После завершения процесса создания необходимо изменить его статус - подготовлен к экспертной оценке.</p> 
     ";
     echo "&nbsp;<a href='index.php?op=chsproject&id=".$member['id']."' title='Изменить статус'><img src='img/s_process.png' width='16' height='16'></a></p></td></tr>"; 
    }
    else
    if ($member['status']=='accepted') 
    {
     echo "<tr><td><p class=zag2>Подготовлен к экспертной оценке.</p>
     <p>Проект подготовлен к экспертной оценке и в ближайшее время получит статус - проходит экспертизу.</p>";
     if(defined("IN_ADMIN"))
      echo "&nbsp;<a href='index.php?op=chsproject&id=".$member['id']."' title='Изменить статус'><img src='img/s_process.png' width='16' height='16'></a></p></td></tr>";
     else 
      echo "</td></tr>";
    }
    else
    if ($member['status']=='inprocess') 
    {
     echo "<tr><td><p class=zag2>Проект проходит экспертную оценку.</p>
     <p>После окончания процедуры экспертной оценки (все эксперты ознакомятся и оценят проект) статус 
     проекта изменится - экспертная оценка проекта завершена.</p>";
     if(defined("IN_ADMIN"))
      echo "&nbsp;<a href='index.php?op=chsproject&id=".$member['id']."' title='Изменить статус'><img src='img/s_process.png' width='16' height='16'></a></p></td></tr>"; 
     else 
      echo "</td></tr>";
    }
    else
    if ($member['status']=='finalized') 
    {
     echo "<tr><td><p class=zag2>Экспертная оценка проекта завершена.</p>
     <p class=zag2>Итоговый балл: ".round($member['maxball'],2)."</p>";
     if(defined("IN_ADMIN"))
      echo "&nbsp;<a href='index.php?op=chsproject&id=".$member['id']."' title='Изменить статус'><img src='img/s_process.png' width='16' height='16'></a></p></td></tr>"; 
     else 
      echo "</td></tr>";
    }
    else
    if ($member['status']=='published') 
     echo "<tr><td><p class=zag2>Опубликован в отчете</p></td></tr>";

  echo "</table><div></p>";
  echo "<p><div id='menu_glide' class='menu_glide'>
        <table width='800' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  
    if ($member['status']=='finalized' or $member['status']=='published') 
    {
      echo "<tr class='tableheader'><td><p class=help>Рецензии или комментарии к проекту</p></td></tr>";
      $res5=mysql_query("SELECT * FROM shablondb WHERE memberid='".$member['id']."' ORDER BY puttime");
      if (!$res5) puterror("Ошибка при обращении к базе данных");
      while($param5 = mysql_fetch_array($res5))
      { 
       echo "<tr><td><p class=zag2><b>Рецензия или комментарий от ".data_convert ($param5['puttime'], 1, 1, 0).":</b> ".$param5['info']."</p></td></tr>";
      } 
    }
  
  echo "</table></div></p>";
  echo "<p><table width='800' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  echo "<tr class='tableheader'><td><p class=help>Замечания и комментарии к проекту от экспертов</p></td></tr>";   
  
  $res3=mysql_query("SELECT * FROM comments WHERE projectid='".$member['id']."' ORDER BY cdate DESC");
  while($param3 = mysql_fetch_array($res3))
   { 
      $res4=mysql_query("SELECT userfio FROM users WHERE id='".$param3['expertid']."'");
      $param4 = mysql_fetch_array($res4);
      
      echo "<tr><td><hr><p><b>Эксперт ".$param4['userfio']." от ".data_convert ($param3['cdate'], 1, 1, 0)." оставил комментарий (замечание):</b></p>
      <p>".$param3['content']."</p></td></tr>";   

      // Установим статус- комментарий прочтен
      if ($param3['readed']==0) {
        $query = "UPDATE projects SET readed='1' WHERE id=".$param3["id"];
        mysql_query($query);
      }

   } 

  echo "</table></p>";
  echo"<p align='center'><input type='button' name='close' value='Закрыть' onclick='closeIt()'></p>";
  
  } 
  include "bottomadmin.php";
} else die;  
?>