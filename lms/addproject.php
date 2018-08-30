<?php
  if(defined("IN_USER")) 
  {
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";
$error = "";
$action = "";

$title=$titlepage="Новый проект";

include "topadmin2.php";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];

if (!empty($action))
{
 if ($action=='activation') 
 {
 
   $proarrid = $_POST['paid'];
   $skey = $_POST['secret'];

   $gst1 = mysql_query("SELECT count(*) FROM projects WHERE userid='".USER_ID."' AND proarrid='".$proarrid."'");
   if (!$gst1) puterror("Ошибка при обращении к базе данных");
   $total3 = mysql_fetch_array($gst1);
   $count3 = $total3['count(*)'];

   $gst2 = mysql_query("SELECT * FROM projectarray WHERE id='".$proarrid."'");
   if (!$gst2) puterror("Ошибка при обращении к базе данных");
   $projectarray = mysql_fetch_array($gst2);

   $proarrname = $projectarray['name']; 
   $proarrcomm = $projectarray['comment']; 

   if ($count3<$projectarray['projectcount']) {
   
   if ($projectarray['payment']>0) // Проект платный
   {
    // Проверим ключ
    $gst3 = mysql_query("SELECT * FROM requests WHERE userid='".USER_ID."' AND proarrid='".$proarrid."'");
    if (!$gst3) puterror("Ошибка при обращении к базе данных");
    $totalkeys = mysql_fetch_array($gst3);

    if ($skey === $totalkeys['secretkey'] ) 
    {

     ?>
     <?
     // Покажем проект
     include "addprojectdop.php";

    }
    else
    {
     echo"<p align='center'><br><table class=bodytable border='0' cellpadding=1 cellspacing=1 bordercolorlight=gray bordercolordark=white>\n
     <tr><td><p class=ptd><b><em class=em>Введен неверный секретный ключ проекта.</em></b></td>
     <tr><td align='center'><input type='button' name='close' value='Назад' onclick='history.back()'></td></tr></table>"; 
    }
    
   }
   }
 
 }
 else
 if ($action=='post') 
 {

  $userid = USER_ID;
  $proarrid = $_POST["paid"];
  $skey = $_POST['secret'];

  if (empty($_POST["info"])) 
  {
    $action = ""; 
    $error = "<LI>Вы не указали наименование проекта.\n";
  }

  $res10=mysql_query("SELECT * FROM poptions WHERE proarrid='".$proarrid."' ORDER BY id");
  while($mb10 = mysql_fetch_array($res10))
  {
    $optionsid = $mb10['id'];
    $filedata = $_FILES["file".$optionsid]["name"]; 

    if($_FILES["file".$optionsid]["name"]!=""){ 
          $origfilename = $_FILES["file".$optionsid]["name"]; 
          $filename = explode(".", $_FILES["file".$optionsid]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 

          if ($mb10['filetype']=="file") {
           for($x=0;$x<count($file_types_array);$x++){ 
             if($filenameext==$file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
             } 
            }
           }
           
          if ($mb10['filetype']=="foto") {
           for($x=0;$x<count($photo_file_types_array);$x++){ 
             if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
             } 
            }
           }
            
           

          if($file_ext_allow){ 
            if($_FILES["file".$optionsid]["size"]>$max_file_size){ 
              $error=$error.$origfilename." превышает размер ".$max_file_size." байт<br>"; 
            } 
          }else{ 
            $error=$error.$origfilename." не поддерживается.<br>"; 
          } 
    } 

  }

   if (!empty($error)) 
  {
    print "<P>Во время добавления проекта произошли следующие ошибки:</p>\n";
    print "<UL>\n";
    print $error;
    print "</UL>\n";
    print "<input type='button' name='close' value='Назад' onclick='history.back()'>"; 
    exit();
  }

  $info = $_POST["info"];
  mysql_query("LOCK TABLES projects WRITE");
  mysql_query("SET AUTOCOMMIT = 0");
  $query = "INSERT INTO projects VALUES (0,
  $userid,
  '$info',
  NOW(),'created',0,$proarrid,'$skey',0,0)";
  
  if(!mysql_query($query)) {
      puterror("Ошибка 1 при добавлении проекта.");
  }

  $projectid = mysql_insert_id();
  define('PROJECT_ID',mysql_query("SELECT LAST_INSERT_ID()"));
  mysql_query("COMMIT");
  mysql_query("UNLOCK TABLES");

  require_once ('lib/transliteration.inc');

  writelog("Добавлен проект №".$projectid." (".$info.").");
 
  $res10=mysql_query("SELECT * FROM poptions WHERE proarrid='".$proarrid."' ORDER BY id");
  while($mb10 = mysql_fetch_array($res10))
  {
    $optionsid = $mb10['id'];
    $contentdata = htmlspecialchars($_POST["content".$optionsid], ENT_QUOTES); 

    if($_FILES["file".$optionsid]["name"]!=""){ 
     $filedata = $_FILES["file".$optionsid]["name"]; 
     $realfiledata = transliteration_clean_filename($_FILES["file".$optionsid]["name"],"ru");
     $filesize = $_FILES["file".$optionsid]["size"]; 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
    
    $secure = md5($projectid.$realfiledata);
    $query2 = "INSERT INTO projectdata VALUES (0, 
    $projectid, 
    $optionsid, 
    '$contentdata', 
    '$filedata',
    '$realfiledata',
    $filesize, 
    $proarrid,
    '$secure')";
    
    if(!mysql_query($query2))
    {
      puterror("Ошибка 2 при добавлении проекта.\n".$query2);
      break; 
    }


    if($_FILES["file".$optionsid]["name"]!=""){ 
          $origfilename = $_FILES["file".$optionsid]["name"]; 
          $filename = explode(".", $_FILES["file".$optionsid]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 

          if ($mb10['filetype']=="file") {
           for($x=0;$x<count($file_types_array);$x++){ 
             if($filenameext==$file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
             } 
            }
           }
           
          if ($mb10['filetype']=="foto") {
           for($x=0;$x<count($photo_file_types_array);$x++){ 
             if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
             } 
            }
           }

          if($file_ext_allow){ 
            if($_FILES["file".$optionsid]["size"]<$max_file_size){ 
              if(move_uploaded_file($_FILES["file".$optionsid]["tmp_name"], $upload_dir.$projectid.$realfiledata)){ 
                echo("Файл успешно загружен. - <a href='".$upload_dir.$projectid.$realfiledata."' target='_blank'>".$filedata."</a><br />"); 
              }else{ 
                $error = $error.$origfilename." не был загружен в каталог сервера."; 
              } 
            }else{ 
              $error=$error.$origfilename." превышает установленный размер."; 
            } 
          }else{ 
            $error=$error.$origfilename." не поддерживается."; 
          } 
    } 
  }
  
  // Отправим сообщение экспертам, которые могут оставлять замечания
  $res3=mysql_query("SELECT * FROM projectarray WHERE id='".$proarrid."'");
  if(!$res3) puterror("Ошибка 3 при изменении данных.");
  $projectarray = mysql_fetch_array($res3);
  if ($projectarray['addcomment']==1 and $projectarray['expertmailer']==1) 
  {

  $gst4 = mysql_query("SELECT * FROM proexperts WHERE proarrid='".$proarrid."' ORDER BY id");
  if (!$gst4) puterror("Ошибка при обращении к базе данных");
  while($member4 = mysql_fetch_array($gst4))
  {

    // Отправим уведомление экспертам по почте
 
    $res5=mysql_query("SELECT * FROM users WHERE id='".$member4['expertid']."'");
    if(!$res5) puterror("Ошибка 3 при изменении данных.");
    $expert = mysql_fetch_array($res5);

    $toemail = $expert['email'];

    $title = "Создан новый проект";
    $body = "Здравствуйте ".$expert['userfio']."!\n\nВ экспертной системе ".$site." появился новый проект.\n
    Создатель нового проекта - ".USER_FIO."\n
    Наименование нового проекта - ".$info."\n
    Требуется проверка данного проекта. (Главное меню - Проекты участников)\n
    С уважением, Экспертная система (".$site.")";

    require_once('lib/unicode.inc');

    $mimeheaders = array();
    $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
    $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
    $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
    $mimeheaders[] = 'From: '. mime_header_encode(USER_FIO.' <'.USER_EMAIL.'>');

     if (!empty($toemail))
     {
      if (!mail(
      $toemail,
      mime_header_encode($title),
      str_replace("\r", '', $body),
      join("\n", $mimeheaders)
      )) {
       puterror("Ошибка при отправке сообщения.");
     } 
     }
    }  
  }  
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php?op=projects'>\n";
      print "</HEAD></HTML>\n";
      exit();
}
}

if (empty($action)) 
{
   $proarrid = $_GET['paid'];
   $gst1 = mysql_query("SELECT count(*) FROM projects WHERE userid='".USER_ID."' AND proarrid='".$proarrid."'");
   if (!$gst1) puterror("Ошибка при обращении к базе данных");
   $total3 = mysql_fetch_array($gst1);
   $count3 = $total3['count(*)'];

   $gst2 = mysql_query("SELECT * FROM projectarray WHERE id='".$proarrid."'");
   if (!$gst2) puterror("Ошибка при обращении к базе данных");
   $projectarray = mysql_fetch_array($gst2);
   
   $proarrname = $projectarray['name']; 
   $proarrcomm = $projectarray['comment']; 

   if ($count3<$projectarray['projectcount']) {
   
   if ($projectarray['payment']>0) // Проект платный
   {

    // Начнем сравнение дат
    $date1 = $projectarray['startdate'];
    $date2 = $projectarray['stopdate'];
    $date3 = date("d.m.Y");
    preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$i);
    $day=$i[1][0];
    $month=$i[2][0];
    $year=$i[3][0];
    
    $arr1 = explode(" ", $date1);
    $arr2 = explode(" ", $date2);  

    $arrdate1 = explode("-", $arr1[0]);
    $arrdate2 = explode("-", $arr2[0]);


    $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
    $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
    $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));

     
    if ($timestamp3 >= $timestamp1 and $timestamp3 <= $timestamp2) {
   
    // Сохраним запрос участника и сгенерируем для него секретный ключ
    $gst2 = mysql_query("SELECT count(*) FROM requests WHERE userid='".USER_ID."' AND proarrid='".$proarrid."'");
    if (!$gst2) puterror("Ошибка при обращении к базе данных");
    $totalkeys = mysql_fetch_array($gst2);
    $countkeys = $totalkeys['count(*)'];
    if ($countkeys==0) {
     // Запишем ключ
     $uid = USER_ID;
     $secretkey = generate_password(7);
     
     $query2 = "INSERT INTO requests VALUES (0, 
     $uid,
     $proarrid, 
     '$secretkey', 
     NOW())";
    
     if(!mysql_query($query2))
     {
       puterror("Ошибка 2 при добавлении проекта.\n".$query2);
       break; 
     }
     
    }           
    // Выводим форму активации ключа
    
    ?>
     <form action="index.php?op=addproject" method="post" enctype="multipart/form-data">
     <input type=hidden name=action value=activation>
     <input type=hidden name=paid value=<? echo $_GET['paid']; ?>

<p align='center'><br>
<div id="menu_glide" class="menu_glide">
<table class=bodytable border="0" cellpadding=5 cellspacing=5 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p align="center" class=ptd><b>Уважаемый участник!</b></p>
        <p class=ptd>Экспертная оценка проекта "<? echo $projectarray['name'];?>" является платной услугой. Стоимость услуги экспертной оценки составляет <? echo $projectarray['paysumma']; ?> руб.</p>
        <p class=ptd>Вам необходимо произвести 
        <a class=link href='index.php?op=pay&s=<? echo $projectarray['paysumma']; ?>&paid=<? echo $_GET['paid']; ?>' title='Платежное извещение Сбербанка' target='_blank'>
        оплату услуги экспертной оценки</a> через 
        <a class=link href='http://www.sbrf.ru' target='_blank'><img src='img/sberbank.gif'></a> 
        <a class=link href='index.php?op=pay&s=<? echo $projectarray['paysumma']; ?>&paid=<? echo $_GET['paid']; ?>' title='Платежное извещение Сбербанка' target='_blank'>Печать бланка платежного извещения.</a></p>
        <p class=ptd>После оплаты на Ваш электронный адрес <b><? echo USER_EMAIL; ?></b> будет отправлен <b>секретный ключ</b> проекта.</p>
        </td>
    </tr>
    <tr><td></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Введите секретный ключ проекта *</em></b></td>
    </tr>
    <tr>
        <td><input type=text name='secret' size=15></td>
    </tr>
    <tr align="center">
        <td colspan="3">
            <input type="submit" value="Продолжить">&nbsp;&nbsp;
            <input type="button" name="close" value="Назад" onclick="history.back()"> 
        </td>
    </tr>           
</table></div>
</p>
</form>
    <?
   }
   }
   else
    include "addprojectdop.php";
}
include "bottomadmin.php";
}
}
else die;
?>
