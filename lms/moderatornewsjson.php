<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 
      include "config.php";

      $id = $_POST["newsid"];
      $name = $_POST["newsname"];
      $toemail = $valmail;
      $fio=USER_FIO;
      $title = "Содана новость или страница";
      $body = "Создана новость (страница): ".$name." пользователем ".$fio;

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode(admin.' <'.$valmail2.'>');

      $json['ok'] = '1';  
      
      if (!empty($toemail))
      {
       if (!mail(
        $toemail,
        mime_header_encode($title),
        str_replace("\r", '', $body),
        join("\n", $mimeheaders)
       )) 
          $json['ok'] = '0'; 
      } else 
          $json['ok'] = '0'; 
     
      echo json_encode($json); 

} else die;
?>
