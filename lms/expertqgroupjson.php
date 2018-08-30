<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
   include "config.php";
   require_once('emailmsg.php');
   require_once('lib/unicode.inc');

   $qgid = $_POST["qgid"];
   $ugid = $_POST["ugid"];

   if (!empty($qgid)) { 
           $qgr = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM questgroups WHERE id='".$qgid."' LIMIT 1;");
           $qgrname = mysqli_fetch_array($qgr);
           $qname = $qgrname['name']; 
           mysqli_free_result($qgr);
   }  

   // Отправим сообщения экспертам
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM useremails WHERE usergroupid='".$ugid."' ORDER BY id;");
   while ($param = mysqli_fetch_array($sql)) 
   {
         $to = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, userfio FROM users WHERE email='".$param['email']."' LIMIT 1;");
         $touser = mysqli_fetch_array($to);

         if (!empty($touser))
         {
          $toid = $touser['id'];
          $signature = md5(time().$toid.$tname);  // Уникальная сигнатура сообщения
          $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        ".USER_ID.",
                                        'Экспертиза группы вопросов <strong>".$qname."</strong>!',
                                        'Вам необходимо провести экспертизу группы вопросов <strong>".$qname."</strong>',
                                        0,
                                        NOW(),
                                        '$signature');";
          mysqli_query($mysqli,$query);
         }
         // Отправим сообщение
      
         $title = "Вам отправлен запрос на проведение экспертизы группы вопросов на сайте testlife.org";
         $body = msghead($touser['userfio'], $site);
         $body .= "<p>Вам отправлен запрос на проведение экспертизы группы вопросов <strong>".$qname."</strong></p>";
         $body .= "<p>Провести экспертизу Вы можете в любое удобное для Вас время.</p>";
         $body .= "<p>Если Вы еще не зарегистрированы на сайте <a href='".$site."' target='_blank'>TestLife</a> - пройдите процедуру регистрации через популярные социальные сети.</p>";
         $body .= "<p>Обращаем внимание, что при регистрации (в профиле) необходимо использовать электронную почту: <strong>".$param['email']."</strong>. При использовании в профиле другого адреса, экспертиза тестовых заданий будет недоступна.</p>";
         $body .= "<p><strong>Все тестовые материалы, представленные для экспертизы, являются авторской разработкой. Запрещается разглашение, тиражирование, копирование или иное распространение тестовых материалов без письменного согласия автора.</strong></p>";
         $body .= "<p>Ознакомиться подробнее с процедурой проведения внешней экспертизы тестовых заданий можно <a href='".$site."/h&id=21' target='_blank'>здесь</a>.</p>";
         $body .= msgtail($site);
         $mimeheaders = array();                                                                                                                                                           
         $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
         $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
         $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
         $mimeheaders[] = 'From: '. mime_header_encode('TestLife <'.$valmail.'>');

         mail(
           $param['email'],
           mime_header_encode($title),
           str_replace("\r", '', $body),
           join("\n", $mimeheaders)
         );
        
         mysqli_free_result($to);
      
   }
   mysqli_free_result($sql);
   $json['ok'] = '1';  

} 
else 
 $json['ok'] = '0';  

echo json_encode($json); 
