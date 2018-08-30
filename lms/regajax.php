<?php
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";


  
  $error1 = "";

  // Проверим есть ли такоq email 
  $tot = mysql_query("SELECT max(id) FROM users ORDER BY id;");
  $gst = mysql_query("SELECT count(email) FROM users WHERE email='".strtolower(trim($_POST["email"]))."'");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");
  $totalemail = mysql_fetch_array($gst);
  $countemail = $totalemail['count(email)'];

  if ($countemail>0)
   {
      $json['error'] = "Пользователь с электронным адресом ".$_POST['email']." уже зарегистрирован.";
      $json['ok'] = '0';
      echo json_encode($json);  
      exit;
   }

  // Генерация логина и пароля
    $total = mysql_fetch_array($tot);
    $count = $total['max(id)']+1;

    $login = 'user'.$count;
    $password = generate_password(7);
    $cpassword = md5($password);
  
    $fio = $_POST["fio"];
    $email = strtolower(trim($_POST["email"]));
    $job = $_POST["job"];

    $supervisor = $_POST["supervisor"];
    if (empty($supervisor))
     $supervisor = false;
    
    mysql_query("START TRANSACTION;");
    if ($supervisor)
    {
    // Запрос к базе данных на добавление супервизора 
    $query = "INSERT INTO users VALUES (0,
                                        '$login',
                                        '$cpassword',
                                        '$fio',
                                        'supervisor',
                                        NOW(),
                                        '$email',
                                        0,
                                        '',
                                        NOW(),
                                        '$job',
                                        0,
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        'offline',
                                        1,
                                        '',
                                        '',
                                        0,0,0,0,0,0,0,'','','male',0,'',1);";
    }
    else
    {
    // Запрос к базе данных на добавление пользователя 
    $query = "INSERT INTO users VALUES (0,
                                        '$login',
                                        '$cpassword',
                                        '$fio',
                                        'user',
                                        NOW(),
                                        '$email',
                                        0,
                                        '',
                                        NOW(),
                                        '$job',
                                        0,
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        'offline',
                                        0,
                                        '',
                                        '',
                                        0,0,0,0,0,0,0,'','','male',0,'',0);";
    }
    if(mysql_query($query))
    {
      mysql_query("COMMIT");

      if ($enable_cache) update_cache('SELECT id,userfio,email,usertype FROM users ORDER BY userfio');
      
      if ($supervisor)
       writelog("Зарегистрирован новый супервизор ".$login." (".$fio.").");
      else
       writelog("Зарегистрирован новый пользователь ".$login." (".$fio.").");

      $toemail = $email;
      $title = "Добро пожаловать супервизор в экспертную систему оценки проектов expert03.ru";

      $body = msghead($fio, $site);

      if ($supervisor)
       $body .= "<p>Спасибо за регистрацию в экспертной системе оценки проектов!</p>
       <p>Системой сформированы автоматические данные для входа:</p>
       <p>Логин - ".$login."</p>
       <p>Пароль - ".$password."</p>
       <p>Параметры Вашего аккаунта Вы можете всегда изменить в личном кабинете. В системе Вы имеете статус <strong>супервизора</strong>.</p>
       <p>Вы можете организовать новый конкурс (разработать модель), проводить оценки, экспертизы, тестирования, назначать экспертов и участников. Вы также можете быть экспертом в других проектах или участником проектов.</p>
       <p>Сейчас Вы можете войти в <a href=".$site.">систему</a> и разработать свою первую модель конкурса.</p>";
      else
       $body .= "<p>Спасибо за регистрацию в экспертной системе оценки проектов!</p>
       <p>Системой сформированы автоматические данные для входа:</p>
       <p>Логин - ".$login."</p>
       <p>Пароль - ".$password."</p>
       <p>Параметры Вашего аккаунта Вы можете всегда изменить в личном кабинете. В системе Вы имеете статус <strong>участника</strong>. Участник может создавать собственные проекты и проходить онлайн тестирование.</p>
       <p>Кроме участников в системе еще есть <strong>эксперты</strong> и <strong>супервизоры</strong>. Эксперты осуществляют проверку подготовленных проектов и проводят экспертизу. Супервизоры разрабатывают модели, на основе которых можно проводить онлайн конкурсы, оценки, экспертизы и тестирования. Супервизоры также назначают экспертов и участников. Вы можете стать супервизором и попробовать все возможности системы через бесплатный тариф.</p>";

      $body .= msgtail($site);

      $fromid = USER_ID;

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

      mail($toemail,
        mime_header_encode($title),
        str_replace("\r", '', $body),
        join("\n", $mimeheaders)); 

      $toemail = $valmail;
      $title = "Зарегистрирован новый пользователь";
      $body = "Зарегистрирован новый пользователь - ФИО: ".$fio."\n
      логин - ".$login."\n
      пароль - ".$password."\n
      email - ".$email."\n";

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode(admin.' <'.$valmail2.'>');

      mail($toemail,
        mime_header_encode($title),
        str_replace("\r", '', $body),
        join("\n", $mimeheaders));   
    }
    else
    {
      mysql_query("COMMIT");
      $json['error'] = "Ошибка обращения к базе данных.";
      $json['ok'] = '0';
      echo json_encode($json);  
      exit;
    }
 
 $json['ok'] = '1';
 echo json_encode($json);  
    
?>
