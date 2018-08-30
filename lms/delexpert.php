<?php
if(!defined("IN_ADMIN")) die;  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  // Формируем SQL-запрос
  $query = "DELETE FROM users
            WHERE id=".$_GET["id"];
  // Удаляем  $id
  if(mysql_query($query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=experts&start=".$_GET["start"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("Ошибка при обращении к базе данных");
?>