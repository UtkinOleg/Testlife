<?
  include "config.php";
  include "func.php";

  $email = strtolower(trim($_POST["email"]));

  $gst = mysql_query("SELECT count(email) FROM users WHERE email='".$email."' LIMIT 1;");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $totalemail = mysql_fetch_array($gst);
  $countemail = $totalemail['count(email)'];

  if ($countemail==0)
   {
      $json['error'] = "Пользователя с таким электронным адресом не существует.";
      $json['ok'] = '0';
      echo json_encode($json);  
      exit;
   }

  $query = mysql_query("SELECT * FROM users WHERE email='".$email."' LIMIT 1;");
  if ($query)
  {
      $user = mysql_fetch_array($query);
      $ls = ceil(strlen($user['password'])/3);
      $s1 = substr($user['password'], 0, $ls);
      $s2 = substr($user['password'], $ls, $ls);
      $s3 = substr($user['password'], $ls+$ls, $ls);

      $cs = md5($user['email']);
      $ls = ceil(strlen($cs)/3);
      $e1 = substr($cs, 0, $ls);
      $e2 = substr($cs, $ls, $ls);
      $e3 = substr($cs, $ls+$ls, $ls);

      writelog("Запрос на изменение пароля для ".$user['userfio']." (".$email.").");

      $title = "Ссылка на изменение пароля на сайте expert03.ru";

      $body = msghead($user['userfio'], $site);
      $body .= "<p>Вы запросили изменение пароля в экспертной системе оценки проектов</p>
      <p>Ваш логин в системе: ".$user['username']."</p>
      <p>Для изменения пароля перейдите по адресу - <a href='".$site."/?restoreme=".$cs."' target='_blank'>".$site."/?restoreme=".$cs."</a></p>";
      $body .= msgtail($site);

      
      require_once('lib/unicode.inc');
      
      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

      mail($email,
      mime_header_encode($title),
      str_replace("\r", '', $body),
      join("\n", $mimeheaders));
      
      
	 } 
   else 
   {
      $json['error'] = "Ошибка обращения к базе данных.";
      $json['ok'] = '0';
      echo json_encode($json);  
      exit;
   }
 
 $json['ok'] = '1';
 echo json_encode($json);  


?>