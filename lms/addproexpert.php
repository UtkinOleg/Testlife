<?php
if (defined("IN_ADMIN")) { 
// Устанавливаем соединение с базой данных
include "config.php";

$error = "";
$action = "";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  $paid = $_POST["paid"];

  if (!empty($_FILES["xmlfile"]["name"]))
  { 
    require_once ('lib/transliteration.inc');
    if($_FILES["xmlfile"]["name"]!=""){ 
     $filedata = $_FILES["xmlfile"]["name"]; 
     $realfiledata = transliteration_clean_filename($_FILES["xmlfile"]["name"],"ru");
     $filesize = $_FILES["xmlfile"]["size"]; 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if($_FILES["xmlfile"]["name"]!=""){ 
          $origfilename = $_FILES["xmlfile"]["name"]; 
          $filename = explode(".", $origfilename); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          if($filenameext=='xml') 
              $file_ext_allow = TRUE; 

          if($file_ext_allow){ 
            if($_FILES["xmlfile"]["size"]<$max_file_size){ 
              if(move_uploaded_file($_FILES["xmlfile"]["tmp_name"], $xmlupload_dir.$_POST["qgid"].$realfiledata)){ 
                echo("Файл успешно загружен. - <a href='".$xmlupload_dir.$_POST["qgid"].$realfiledata."' target='_blank'>".$filedata."</a><br />"); 
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
   
   if (file_exists($xmlupload_dir.$_POST["qgid"].$realfiledata)) {
    $xml = simplexml_load_file($xmlupload_dir.$_POST["qgid"].$realfiledata);
 
    foreach ($xml->xpath('//expert') as $expert) 
    {
     echo '<p>Запись эксперта id <b>'.$expert->id.'</b></p>';
     
     $tot2 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$paid."' AND expertid='".$expert->id."'");
     $tot2cnt = mysql_fetch_array($tot2);
     $countpr = $tot2cnt['count(*)'];
     if ($countpr==0)
     {  
     // Запишем эксперта в базу
     if (!empty($expert->id))
     {
      $eid = $expert->id;
      mysql_query("LOCK TABLES proexperts WRITE");
      mysql_query("SET AUTOCOMMIT = 0");
      $query = "INSERT INTO proexperts VALUES (0,
                                        $eid,
                                        $paid);";
      if(!mysql_query($query)) {
        echo "<p>Ошибка при добавлении эксперта <b>".$eid.'</b></p>';
      }
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");      
    
      $gst3 = mysql_query("SELECT * FROM users WHERE id='".$eid."'");
      if (!$gst3) puterror("Ошибка при обращении к базе данных");
      $user = mysql_fetch_array($gst3);
      if ($user['usertype']=='user')
      {
       mysql_query("LOCK TABLES users WRITE");
       mysql_query("SET AUTOCOMMIT = 0");
       mysql_query("UPDATE users SET usertype = 'expert' WHERE id='".$eid."'");
       mysql_query("COMMIT");
       mysql_query("UNLOCK TABLES");
      }
    
     } else
       echo '<p>Параметр пустой</p>';
     } else
       echo '<p>Эксперт id <b>'.$expert->id.' уже есть в списке</b></p>';

    }
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");   

    
    } else {
      $error=$error.' Не удалось открыть файл '.$xmlupload_dir.$_POST["qgid"].$realfiledata;
   }   

 

   if (!empty($error)) 
  {
    echo '<script language="javascript">';
    echo 'alert("Ошибки при выполнении запроса: '.$error.'");
    parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
    echo '</script>';
    exit();
  }   

  echo '<script language="javascript">';
  echo 'parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
  echo '</script>';
  exit();

 }
 else
 {
 // *********************************************************************************************************

  $userid = $_POST["userid"];
  

  if (!empty($userid))
  {
 
   $tot2 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$paid."' AND expertid='".$userid."'");
   $tot2cnt = mysql_fetch_array($tot2);
   $countpr = $tot2cnt['count(*)'];
   if ($countpr==0)
   {  
    mysql_query("LOCK TABLES proexperts WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "INSERT INTO proexperts VALUES (0,
                                        $userid,
                                        $paid);";
    if(mysql_query($query))
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
   
      $gst3 = mysql_query("SELECT * FROM users WHERE id='".$userid."'");
      if (!$gst3) puterror("Ошибка при обращении к базе данных");
      $user = mysql_fetch_array($gst3);
      if ($user['usertype']=='user')
      {
       mysql_query("LOCK TABLES users WRITE");
       mysql_query("SET AUTOCOMMIT = 0");
       mysql_query("UPDATE users SET usertype = 'expert' WHERE id='".$userid."'");
       mysql_query("COMMIT");
       mysql_query("UNLOCK TABLES");
      }

      $toemail = $user['email'];
      if (!empty($toemail))
       {
        $pa = mysql_query("SELECT name FROM projectarray WHERE id='".$paid."'");
        if (!$pa) puterror("Ошибка при обращении к базе данных");
        $pa1 = mysql_fetch_array($pa);

        $title = "Присвоение статуса эксперта";
        $body = "Здравствуйте ".$user['userfio']."!\n\n
        
Вам присвоен статус эксперта в экспертной системе оценки проектов (".$site.") для оценки проектов '".$pa1['name']."'.\n
        
Для экспертизы проектов '".$pa1['name']."'. вам необходимо войти в систему ".$site." (кнопка вход на сайте).\n 

Для просмотра проектов в левой части появится меню 'Проекты участников' - '".$pa1['name']."'.\n 

Для проведения экспертизы проектов в левой части появится также меню 'Экспертиза проектов' - '".$pa1['name']."'.\n
После выбора появится ссылка 'Новая экспертиза' и откроется блок проведенных экспертиз. Нажав на ссылку 'Новая экспертиза', будет предложено выбрать один из проектов участников. Затем нажать на кнопку 'Далее'.\n 
Откроется проект участника и критерии оценки проекта (экспертный лист).\n 

Необходимо оценить предложенный проект, установить критерии, заполнить комментарий эксперта и нажать кнопку 'Сохранить'.\n 
По мере оценки проектов, список проектов для экспертизы будет уменьшаться. Необходимо оценить все проекты.\n

Дополнительно, системой автоматически формируется рейтинг проектов, который также могут видеть все участники.\n
       
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
      // Возвращаемся на главную страницу если всё прошло удачно
      echo '<script language="javascript">';
      echo 'parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
      echo '</script>';
      exit();
      
      
    }
    else
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      echo '<script language="javascript">';
      echo 'alert("Ошибки при выполнении запроса.");
      parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
      echo '</script>';
      exit();
    }
   }
   else
    {
      echo '<script language="javascript">';
      echo 'parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
      echo '</script>';
      exit();
    }
    
  } 
  else
  {
      echo '<script language="javascript">';
      echo 'alert("Ны выбран эксперт.");
      parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/proexperts&paid='.$paid.'");';
      echo '</script>';
      exit();
  }
 }  
  
}
else
if (empty($action)) 
{

  $tableheader = "class=tableheader";
  $showhide = "";
  $tableheader = "class=tableheaderhide";
  $paid = $_GET["paid"];
  
require_once "header.php"; 
?>
<script src="scripts/chosen.jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="scripts/chosen.css">
<script type="text/javascript">
 $(function(){
    $(".chosen-select").chosen();
});
</script>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
</style>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Прикрепить эксперта к модели проекта</p></h3>
<div id="menu_glide" class="menu_glide">
<form action="addproexpert" method="post" enctype="multipart/form-data">
<table class=bodytable border="0" width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<tr><td>
<input type=hidden name=action value=post>
<input type=hidden name=paid value=<? echo $paid; ?>>
<table class=bodytable border="0" width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b>Выбранный пользователь:</b></p></td>
    </tr><tr>    
        <td><select name="userid" class="chosen-select">
        <? 

if ($enable_cache) {
 
 require_once('cache.php');
 $cache = new Cache();
 $sql = 'SELECT id,userfio,email,usertype FROM users ORDER BY userfio';
 $filename = md5($sql) . '_' . strlen($sql) . '.tmp';

 $data = $cache->read($filename);
 if (empty($data)) update_cache($sql); 
         
 echo "<option value=''>Выбрать эксперта</option>";
 foreach($data as $row) 
  echo "<option value='".$row['id']."'>".$row['userfio']." (".$row['email'].") ".$row['usertype']."</option>";

        }
        else
        {
           $gst2 = mysql_query("SELECT * FROM users ORDER BY userfio");
           while($member = mysql_fetch_array($gst2))
            echo "<option value='".$member['id']."'>".$member['userfio']." (".$member['email'].") ".$member['usertype']."</option>";
        }
        ?>
        </select>
        </td>
    </tr>
    
    <tr><td>
        <p class=ptd><b>Или загрузить список экспертов из файла XML</b> 
        Размер файла не должен превышать 1Мб. Приоритет загрузки - XML файл</p>
        </td>
    </tr><tr>    
        <td>
        <input type='file' name='xmlfile'/>
    </td></tr>

    <tr><td>

     <script> 
      $(function() {
       $( "#accordion" ).accordion({heightStyle: "content",collapsible: true, active: false
      });
      });
      </script>
      
<div id='accordion'><h3 style='font-size:12px; color: #fff;'><b>Пример файла XML</b></h3><div>
<xmp style="font-size: 12px;">
<?echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";?>
<experts>
  <expert> 
    <id>1</id> 
  </expert>
  <expert> 
    <id>2</id> 
  </expert>
</experts>
</xmp>
</div></div>
    
    </td></tr>
</table>
</td></tr>
    <tr align="center">
        <td>
            <input type="submit" value="Добавить">
        </td>
    </tr>           
</table></form>
</div>
</td></tr></table>
</body></html>
<?
}
} else die;
?>
