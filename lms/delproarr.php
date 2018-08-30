<?php
if(defined("IN_SUPERVISOR") || defined("IN_ADMIN")) 
{  
  include "config.php";
  include "func.php";

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$_GET['id']."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {
  mysql_query("START TRANSACTION;");
  $query = "DELETE FROM grades WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 1");
  $query = "DELETE FROM shablongroups WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 2");
  $query = "DELETE FROM shablon WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 3");
  $query = "DELETE FROM poptions WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 4");
  $query = "DELETE FROM proexperts WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 5");
  $query = "DELETE FROM prousers WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 5");
  $query = "DELETE FROM requests WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 6");
  $query = "DELETE FROM projectbank WHERE proarrid=".$_GET["id"];
  if(!mysql_query($query)) puterror("Ошибка при обращении к базе данных 7");
  $query2 = "DELETE FROM projectarray WHERE id=".$_GET["id"];
  if(mysql_query($query2))
   {
      mysql_query("COMMIT");
      if ($enable_cache) 
       update_cache('SELECT a.id, a.name FROM projectarray as a WHERE a.closed=0 ORDER BY a.id DESC');
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=parray'>\n";
      print "</HEAD></HTML>\n";
   }
  else puterror("Ошибка при обращении к базе данных 8");

 }
}
else die;
?>