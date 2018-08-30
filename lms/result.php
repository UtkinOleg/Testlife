<?
include "config.php";
include "func.php";

// регистрационная информация (пароль #2)
// registration info (password #2)
$mrh_pass2 = "jgkfnf2";

//установка текущего времени
//current date
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];   //PaID
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
// проверка корректности подписи
// check signature
if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}

// признак успешно проведенной операции

/*    mysql_query("LOCK TABLES money WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "INSERT INTO money VALUES (0,
                                        $inv_id,
                                        $shp_item,
                                        $out_summ, 
                                        '$date');";
    if (!mysql_query($query)) 
    {
     echo "bad query\n";
     exit();
    }
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    
    
      $gst3 = mysql_query("SELECT * FROM users WHERE id='".USER_ID."'");
      if (!$gst3) puterror("Ошибка при обращении к базе данных");
      $user = mysql_fetch_array($gst3);

      $toemail = $user['email'];
      if (!empty($toemail))
       {
        $pa = mysql_query("SELECT name FROM projectarray WHERE id='".$shp_item."'");
        if (!$pa) puterror("Ошибка при обращении к базе данных");
        $pa1 = mysql_fetch_array($pa);

        $title = "Оплата услуги по размещению проекта";
        $body = "Здравствуйте ".$user['userfio']."!\n\n
        
Вами произведена оплата в размере ".$sum." руб. за размещение проекта '".$pa1['name']."'.\n
        
Теперь Вы может войти в систему и приступить к созданию проекта.\n
       
С уважением, Экспертная система (".$site.")\n";

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
*/
?>


