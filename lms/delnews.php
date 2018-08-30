<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {
  include "config.php";
  include "func.php";

 $id = $_GET['id'];
 $query = "SELECT * FROM news WHERE id='".$id."' LIMIT 1;";
 $gst = mysql_query($query);
  if ($gst)
    $member = mysql_fetch_array($gst);
  else 
    puterror("Ошибка при обращении к базе данных");
  if (defined("IN_ADMIN"))
   $query = "DELETE FROM news WHERE id=".$_GET["id"];
  else
  if (!$member['published'])
   $query = "DELETE FROM news WHERE id=".$_GET["id"];
  if(mysql_query($query))
  {
      mysql_query("COMMIT");
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=newses'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("Ошибка при обращении к базе данных");
} else die;
?>