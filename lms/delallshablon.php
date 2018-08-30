<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $paid = $_GET["id"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Формируем SQL-запрос
  $query = "DELETE FROM shablon
            WHERE proarrid=".$paid;
  // Удаляем  $id
  if(mysql_query($query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=shablons&paid=".$paid."&tab=2'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("Ошибка при обращении к базе данных");
 }
} else die;  
?>