<?
if(defined("USER_REGISTERED")) 
{  
  include "config.php";
  include "func.php";

  $id = $_POST["msgid"];
  $userid = $_POST["userid"];

  if(!mysql_query("UPDATE msgs SET readed = '1' WHERE id=".$id))
    $json['ok'] = '0'; 

  $from = mysql_query("SELECT * FROM users WHERE id='".$userid."' LIMIT 1;");
  $fromuser = mysql_fetch_array($from);
  $msg = mysql_query("SELECT * FROM msgs WHERE id='".$id."' LIMIT 1;");
  $msguser = mysql_fetch_array($msg);
  $s="";
  $s.="<div id='menu_glide' style='padding:10px; background: #e6e6e6;' class='ui-widget-content ui-corner-all'><b><p>Тема: ".$msguser['title']."</p>";
  $s.="<p>От: ".$fromuser['userfio']."</p>";
  $s.="<p>Дата: ".data_convert ($msguser['msgdate'], 1, 0, 0)."</p></b>";
  $s.="<p>".$msguser['body']."</p></div>";
  mysql_free_result($from);
  mysql_free_result($msg);
  $json['content'] = htmlspecialchars_decode($s);  
  
  if(!empty($json['content']))  { 
             $json['ok'] = '1';  
  } else {  
             $json['ok'] = '0'; 
  }      
  echo json_encode($json); 
}
?>