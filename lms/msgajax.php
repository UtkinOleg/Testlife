<?
  $body = $_POST["body"];
  $email = $_POST["email"];   
  $email = strtolower(trim($email));
  if (!empty($email))
  {
  $fio = $_POST['name'];

  $title = $_POST["title"];
  if (empty($title))
   $title = "Cообщение";

  require_once('lib/unicode.inc');
  $mimeheaders = array();
  $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
  $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
  $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
  $mimeheaders[] = 'From: '. mime_header_encode('info@testlife.org');
  $json['ok'] = '0';

 if (!empty($email))
 {
 
  if (mail('siberia-soft@yandex.ru',
      mime_header_encode($title),
      str_replace("\r", '', $fio.' ('.$email.') сообщает: '.$body),
      join("\n", $mimeheaders))) 
    $json['ok'] = '1';
  else   
    $json['ok'] = '0';
 
 } 
 else 
   $json['ok'] = '0'; 
 
 } else 
    $json['ok'] = '0';
 
 echo json_encode($json);  
