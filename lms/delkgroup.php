<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Формируем SQL-запрос
  $query = "DELETE FROM shablongroups
            WHERE id=".$_GET["id"];
  // Удаляем  $id
  if(mysql_query($query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=shablons&paid=".$_GET["paid"]."&tab=1'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("Ошибка при обращении к базе данных");
  }
} else die;  
?>