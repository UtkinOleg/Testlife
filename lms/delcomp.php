<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";

  $paid = $_GET["paid"];
  $shid = $_GET["shid"];
  $id = $_GET["id"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
    $query = "DELETE FROM shabloncparams WHERE shabloncid=".$id;
    mysql_query($query);
    $query = "DELETE FROM shabloncomplex WHERE id=".$id;
    mysql_query($query);
    print "<HTML><HEAD>\n";
    print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=edcomplex&paid=".$paid."&id=".$shid."'>\n";
    print "</HEAD></HTML>\n";
   }
} else die;  
?>