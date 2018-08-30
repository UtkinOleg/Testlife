<?php
  include "config.php";
  include "func.php";
  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {
  // Получаем соединение с базой данных
  // Формируем SQL-запрос
  mysql_query("LOCK TABLES money WRITE");
  mysql_query("SET AUTOCOMMIT = 0");
  $query = "DELETE FROM money
            WHERE id=".$_GET["id"];
  // Удаляем  $id
  if(mysql_query($query))
  {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=oplata&paid=".$paid."'>\n";
      print "</HEAD></HTML>\n";
  }
  else 
  {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      puterror("Ошибка при обращении к базе данных");
  }
  }
?>