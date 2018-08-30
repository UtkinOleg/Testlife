<?
 include "config.php";

  function msghead($fio, $site)
  {
    $s='<body style="margin:0; padding:0;">
   <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background-color:#F8F8F8;" align="center"><tr>
     <td style="margin:0; padding:0;">
     <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;" align="center"><tr>
      <td height="10"/></tr>
      <tr><td align="center">
      <h1 style="margin-top:10px;margin-bottom:10px;">
        <a href="'.$site.'" title="Создание тестов онлайн" target="_blank"><img src="'.$site.'/img/testlife.png"></a>
      </h1>
     </td></tr>
    </table>
   </td></tr>
  </table>
   <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;background-color:#FFF;" align="center"><tr>
   <tr><td> 
      <table cellpadding="0" cellspacing="0" width="500" style="padding: 0 1em; font: 14px / 1.4 \'Helvetica\', \'Arial\', sans-serif;color:#000;border-collapse:collapse;margin:0">
      <tr>
       <td align="center" colspan="3" valign="top">
        <br><p style="font-size:1.5em;">Здравствуйте '.$fio.'!</p><br>
       </td>
      </tr>
      <tr>
       <td align="justify" colspan="3" valign="bottom">';
    return $s;
  }
  
  function msgtail($site)
  {
   $s='<p>Это информационное сообщение - отвечать на него не нужно.</p>
        <hr>
        <p>С уважением, <a href="'.$site.'" target="_blank">Test Life</a></p>
       </td>
      </tr>
      </table>
   </td></tr>
   </table>
   </body>';
   return $s;
  }

 // регистрационная информация (пароль #1)
 // registration info (password #1)
 $mrh_pass1 = "O2nJI_yI3hb";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";


$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
if ($my_crc != $crc)
{
  echo "bad sign\n";
  exit();
}

if ($shp_item>0)
{

// признак успешно проведенной операции

       mysqli_query($mysqli,"START TRANSACTION;");
       mysqli_query($mysqli,"UPDATE orders SET paid = 1 WHERE id='".$inv_id."'");
       $query = "INSERT INTO money VALUES (0,
                                        $inv_id,
                                        $shp_item,
                                        $out_summ, 
                                        '$date');";
       if (!mysqli_query($mysqli,$query)) 
       {
         echo "bad query\n";
         exit();
       }
       mysqli_query($mysqli,"COMMIT;");
    
       $order1 = mysqli_query($mysqli,"SELECT userid FROM orders WHERE id='".$inv_id."' LIMIT 1;");
       $o1 = mysqli_fetch_array($order1);
       if ($o1['userid']==USER_ID) 
       {

        $to = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".USER_ID."' LIMIT 1;");
        $touser = mysqli_fetch_array($to);

         if (!empty($testid)) { 
           $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM testgroups WHERE id='".$shp_item."' LIMIT 1;");
           $testname = mysqli_fetch_array($test);
           $tname = $testname['name']; 
           mysqli_free_result($test);
         }  
       
         // Отправим сообщение
         require_once('lib/unicode.inc');
      
         $title = "Успешная оплата услуги тестирования на сайте testlife.org";
         $body = msghead($touser['userfio'], $site);
         $body .='<p>Вами успешно произведена оплата в размере '.$out_summ.' руб. за услугу тестирования на сайте testlife.org.</p>';
         if ($out_summ==50)
          $body .= "<p>Теперь можно пройти зачетный тест <a href='".$site."/ts'><strong>".$tname."</strong></a></p>";
         $body .= msgtail($site);
         $mimeheaders = array();
         $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
         $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
         $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
         $mimeheaders[] = 'From: '. mime_header_encode('TestLife <'.$valmail.'>');

         mail(
           USER_EMAIL,
           mime_header_encode($title),
           str_replace("\r", '', $body),
           join("\n", $mimeheaders)
          );
          
         mysqli_free_result($to);
       }
       mysqli_free_result($order1);
 }     
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=ts'>\n";
      print "</HEAD></HTML>\n";


