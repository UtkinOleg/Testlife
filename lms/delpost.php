<?php
if(!defined("IN_ADMIN")) die;  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  $query = "DELETE FROM users WHERE id=".$_GET["id"];
  mysql_query("LOCK TABLES users WRITE");
  mysql_query("SET AUTOCOMMIT = 0");
  if(mysql_query($query))
  {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      if ($enable_cache) update_cache('SELECT id,userfio,email,usertype FROM users ORDER BY userfio');

      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=members&start=".$_GET["start"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  else 
  {
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    puterror("Ошибка при обращении к базе данных");
  }  
?>