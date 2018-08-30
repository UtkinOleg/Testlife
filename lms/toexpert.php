<?php
if(defined("USER_REGISTERED")) {  

  include "config.php";
  include "func.php";

  $title=$titlepage="Стать экспертом";
  include "topadmin.php";

  if (empty($_GET['key']))
   $key = $_POST['key'];
  else 
   $key = $_GET['key'];

  if (empty($_GET['userid']))
   $userid = $_POST['userid'];
  else 
   $userid = $_GET['userid'];

  $gst = mysql_query("SELECT * FROM projectarray WHERE expertkey='".$key."'");
  if (!$gst) puterror("Ошибка при обращении к базе данных 1");
  $member = mysql_fetch_array($gst);
  $beex=0;
  $prarrname = "";
  
    // Проверка на существующего эксперта
    $gst2 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$member['id']."' AND expertid='".USER_ID."'");
    $user = mysql_fetch_array($gst2);
    if ($user['count(*)']==0) 
    {
     $paid = $member['id']; 
     
     mysql_query("LOCK TABLES proexperts WRITE");
     mysql_query("SET AUTOCOMMIT = 0");
     $query = "INSERT INTO proexperts VALUES (0, $userid, $paid);";
     if(mysql_query($query))
     {
       $beex=1;
       $prarrname = $member['name'];
       
//       writelog("Пользователь стал экспертом шаблона через ключ ".$key.".");
       
       if(defined("IN_USER")) {
         if(!defined("IN_EXPERT") && !defined("IN_SUPERVISOR") && !defined("IN_ADMIN")) {
          mysql_query("LOCK TABLES users WRITE");
          mysql_query("SET AUTOCOMMIT = 0");
          if(!mysql_query("UPDATE users SET usertype = 'expert' WHERE id=".$userid))
           puterror("Ошибка при обращении к базе данных 2");
          mysql_query("COMMIT");
          mysql_query("UNLOCK TABLES");
         }
         define("IN_EXPERT", TRUE); 
        } 
      }    
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
     }
     else
      echo "<p align='center'>Вы уже являетесь экспертом проектов.</p>";
  
  if ($beex==1) {
   
      $toemail = USER_EMAIL;
      if (!empty($toemail))
       {
        $title = "Присвоение статуса эксперта";
        $body = "Здравствуйте ".USER_FIO."!\n\n
        Вам присвоен статус эксперта в экспертной системе оценки проектов (".$site.") для оценки проектов '".$prarrname."'.\n
        Для проведения экспертизы необходимо войти в систему, выбрать пункт 'Экспертиза проектов', далее в выпадающем меню выбрать пункт '".$prarrname."' и оценить все представленные проекты.\n
        Представленные проекты необходимо оценить по установленным критериям в экспертном листе. Каждый проект можно оценить только один раз!.\n
        С уважением, Экспертная система (".$site.")";

        require_once('lib/unicode.inc');
  
        $mimeheaders = array();
        $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
        $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
        $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
        $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

        if (!empty($toemail))
        {
         mail($toemail,
         mime_header_encode($title),
         str_replace("\r", '', $body),
         join("\n", $mimeheaders));
        }     
       }
   
   echo "<p align='center'>Вы присоединились к сообществу экспертов.</p>";
   echo "<p align='center'>Теперь Вам доступны новые функции системы - <b>экспертиза проектов</b> и <b>проекты участников</b>.</p>";
  }
  else
  {
   echo "<p align='center'>Ключ эксперта введен неправильно.</p>";
  }
    ?>
    <center><input type="button" name="close" value="Продолжить" onclick="document.location='<? echo $site; ?>/news'"></center>
    <?    
  include "bottomadmin.php";

} else die;  
?>