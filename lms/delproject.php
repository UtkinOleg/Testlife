<?php

if(defined("USER_REGISTERED")) 
{  

  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  // Дополнительная защита от удаления
  $z=0;
  $gst = mysql_query("SELECT id FROM projects WHERE userid='".USER_ID."'");
   while($paramid = mysql_fetch_array($gst))
     if ($_GET['id']==$paramid['id']) $z++;
   
  if ($z>0 || defined("IN_ADMIN")) 
  { 

  $id = $_GET['id'];
  if (empty($id)) die;

  $res3=mysql_query("SELECT * FROM projectdata WHERE projectid='".$id."' ORDER BY id");
  if(!$res3) puterror("Ошибка 3 при удалении проекта.");
   while($param = mysql_fetch_array($res3))
    { 
     if (!empty($param['filename'])) { 
      unlink($upload_dir.$id.$param['realfilename']);
     } 
    }

  $res3=mysql_query("SELECT * FROM multiprojectdata WHERE projectid='".$id."' ORDER BY id");
  if(!$res3) puterror("Ошибка 3 при удалении проекта.");
   while($param = mysql_fetch_array($res3))
    { 
     if (!empty($param['filename'])) { 
      unlink($upload_dir.$id.$param['realfilename']);
     } 
    }

  $query = "DELETE FROM multiprojectdata WHERE projectid=".$id;
  if (!mysql_query($query)) die;

  $query = "DELETE FROM projectdata WHERE projectid=".$id;
  if(mysql_query($query))
  {
   $query2 = "DELETE FROM projects WHERE id=".$id;
   if(mysql_query($query2))
   {

      writelog("Удален проект №".$id.". Удалил - ".USER_FIO." (".USER_ID.")");
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=projects'>\n";
      print "</HEAD></HTML>\n";
   }
   else die;
  }
  else die;
  }

}
else die;
?>