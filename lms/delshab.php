<?php
if(defined("IN_ADMIN")) {
  include "config.php";
  include "func.php";
  $query = "DELETE FROM adminshab WHERE id=".$_GET["id"];
  if(mysql_query($query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=admshab&mode=".$_GET["mode"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("Ошибка при обращении к базе данных");
} else die;  
?>