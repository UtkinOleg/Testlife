<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  $testid = $_GET["tid"];

  $tg = mysql_query("SELECT ownerid FROM testgroups WHERE id='".$testid."' LIMIT 1;");
  if (!$tg) puterror("Ошибка при обращении к базе данных");
  $tgdata = mysql_fetch_array($tg);

  if ((defined("IN_SUPERVISOR") and $paid == 0 and $tgdata['ownerid'] == USER_ID) or 
  (defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {
   $query = "DELETE FROM testgroups WHERE id=".$testid;
   if (!mysql_query($query))
     puterror("Ошибка при обращении к базе данных");
  } else die;
  
  mysql_free_result($td);
  
  $id = $_GET["qgid"];

  $tg = mysql_query("SELECT userid FROM questgroups WHERE id='".$id."' LIMIT 1;");
  if (!$tg) puterror("Ошибка при обращении к базе данных");
  $tgdata = mysql_fetch_array($tg);

  if ((defined("IN_SUPERVISOR") and $tgdata['userid'] == USER_ID) or defined("IN_ADMIN")) 
  {
  // Удалим вопросы. если есть
  mysql_query("START TRANSACTION;");
  $qst = mysql_query("SELECT id FROM questions WHERE qgroupid='".$id."' ORDER BY id");
  while($member = mysql_fetch_array($qst))
       {
        $query = "DELETE FROM answers WHERE questionid=".$member['id'];
        if (!mysql_query($query))
           puterror("Ошибка при обращении к базе данных");
       }
  mysql_free_result($gst);
  $query = "DELETE FROM questions WHERE qgroupid=".$id;
  mysql_query($query);

  $query = "DELETE FROM questgroups WHERE id=".$id;
  if(mysql_query($query))
  {
      mysql_query("COMMIT");
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=testoptions&paid=".$paid."'>\n";
      print "</HEAD></HTML>\n";
  }
  else 
   puterror("Ошибка при обращении к базе данных");
  }
  else die; 
  
  
} else die;  
?>