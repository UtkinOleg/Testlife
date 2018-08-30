<?php
if(!defined("USER_REGISTERED")) die;  
 
include "config.php";
include "func.php";

$action = $_POST["action"];

if (!empty($action)) 
{
  $id = $_POST["id"];

// if ($id!=USER_ID and !defined("IN_ADMIN"))
//  $title = "Просмотр данных";
// else
//  $title = "Изменение данных";

 $titlepage=$title;  

 include "topadmin.php";

 if ($id != USER_ID and !defined("IN_ADMIN")) die;

 $utype = $_POST["utype"];

     if (empty($_POST["private_fio"]))
        $private_fio=0;
      else
        $private_fio=1;
        
      if (empty($_POST["private_email"]))
        $private_email=0;
      else
        $private_email=1;

      if (empty($_POST["private_region"]))
        $private_region=0;
      else
        $private_region=1;

      if (empty($_POST["private_city"]))
        $private_city=0;
      else
        $private_city=1;

      if (empty($_POST["private_job"]))
        $private_job=0;
      else
        $private_job=1;

      if (empty($_POST["private_person"]))
        $private_person=0;
      else
        $private_person=1;

      if (empty($_POST["private_photo"]))
        $private_photo=0;
      else
        $private_photo=1;
        
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["fio"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели Фамилию Имя Отчество.";
  }
  if (empty($_POST["login"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели логин.";
  }

  // Проверим есть ли такоq email и логин
  $email = $_POST["email"];
  $login = $_POST["login"]; 
  
  if (!empty($email))
  {
   $em = mysql_query("SELECT count(email) FROM users WHERE id!=".$id." AND email='".strtolower(trim($email))."'");
   if (!$em) puterror("Ошибка при обращении к базе данных");
   $totalemail = mysql_fetch_array($em);
   $countemail = $totalemail['count(email)'];
  }
  else 
  $countemail = 0;
  
  if (!empty($login))
  {
   $lo = mysql_query("SELECT count(username) FROM users WHERE id!=".$id." AND username='".strtolower(trim($login))."'");
   if (!$lo) puterror("Ошибка при обращении к базе данных");
   $totallogin = mysql_fetch_array($lo);
   $countlogin = $totallogin['count(username)'];
  }
  else 
  $countlogin = 0;

  if ($countemail>0)
      $error = $error." Такой электронный адрес уже существует.";
  if ($countlogin>0)
      $error = $error." Такой логин уже существует.";


  if($_FILES["photo"]["name"]!=""){ 
          $origfilename = $_FILES["photo"]["name"]; 
          $filename = explode(".", $_FILES["photo"]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          for($x=0;$x<count($photo_file_types_array);$x++){ 
            if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
            } 
          } 

          if($file_ext_allow){ 
            if($_FILES["photo"]["size"]>104800){ 
              $error=$error." ".$origfilename." превышает размер 100 кбайт."; 
            } 
          }else{ 
            $error=$error." ".$origfilename." не поддерживается."; 
          } 
    } 

   if (!empty($error)) 
   {
    echo '<script language="javascript">';
    echo 'alert("Ошибки при изменении данных: '.$error.'");';
    echo '</script>';
    print "<HTML><HEAD>\n";
    print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=edituser'>\n";
    print "</HEAD></HTML>\n";
    exit();
   }

    // Проверка пользователя на участие в проектах и экспертизах
    check_user_in_system($_POST["email"]);

   mysql_query("LOCK TABLES users WRITE");
   mysql_query("SET AUTOCOMMIT = 0");
   if (defined("IN_ADMIN")) {
      $query = "UPDATE users SET username = '".$_POST["login"]."'
            , userfio = '".$_POST["fio"]."' 
            , email = '".$_POST["email"]."' 
            , job = '".$_POST["job"]."' 
            , person = '".$_POST["person"]."' 
            , usertype = '".$_POST["usertype"]."'     
            , region = '".$_POST["region"]."' 
            , city = '".$_POST["city"]."' 
            , info = '".$_POST["info"]."'"; 
     if (!empty($_POST["pass"])) 
       $query .= ", password = '".md5($_POST["pass"])."'";
     $query .= ", pacount = ".$_POST["pacount"];
     $query .= ", qcount = ".$_POST["qcount"];
     $query .= " WHERE id=".$_POST["id"];
   } else
   {
      $query = "UPDATE users SET username = '".$_POST["login"]."'
            , userfio = '".$_POST["fio"]."' 
            , email = '".$_POST["email"]."' 
            , job = '".$_POST["job"]."' 
            , person = '".$_POST["person"]."' 
            , region = '".$_POST["region"]."' 
            , city = '".$_POST["city"]."' 
            , info = '".$_POST["info"]."' 
            , private_fio = ".$private_fio."
            , private_email = ".$private_email." 
            , private_region = ".$private_region." 
            , private_city = ".$private_city." 
            , private_job = ".$private_job." 
            , private_person = ".$private_person." 
            , private_photo = ".$private_photo." 
           WHERE id=".$_POST["id"];
   }

   
   if(mysql_query($query))
   {
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    
    if ($enable_cache) update_cache('SELECT id,userfio,email,usertype FROM users ORDER BY userfio');

    writelog('Изменились личные данные - '.$_POST["fio"]);
    
    require_once ('lib/transliteration.inc');

    $res3=mysql_query("SELECT * FROM users WHERE id='".$_POST["id"]."'");
    if(!$res3) puterror("Ошибка 3 при изменении проекта.");
    $param = mysql_fetch_array($res3);

    if($_FILES["photo"]["name"]!=""){ 
     $filedata = $_FILES["photo"]["name"]; 
     $realfiledata = transliteration_clean_filename($_FILES["photo"]["name"],"ru");
     $filesize = $_FILES["photo"]["size"]; 

     // Удалим файл - есди пользователь заменил его
     if (!empty($param['photoname'])) { 
      unlink($photo_upload_dir.$_POST["id"].$param['photoname']);
     }
     
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }

   
    if (!empty($param['photoname']) && !empty($_FILES["photo"]["name"])) { 

     $query2 = "UPDATE users SET photoname = '".$realfiledata."' WHERE id='".$_POST["id"]."'";
     
     if(!mysql_query($query2))
     {
      puterror("Ошибка 2 при изменении проекта.\n".$query2);
      break; 
     }
     
     writelog('Изменилась фотография пользователя.');
     
     } else {
    if (empty($param['photoname']) && !empty($_FILES["photo"]["name"])) { 
      $query2 = "UPDATE users SET photoname = '".$realfiledata."' 
           WHERE id='".$_POST["id"]."'";
      if(!mysql_query($query2))
     {
      puterror("Ошибка 2 при изменении проекта.\n".$query2);
      break; 
     }
     writelog('Изменилась фотография пользователя.');

     } 
    }


    if($_FILES["photo"]["name"]!=""){ 
          $origfilename = $_FILES["photo"]["name"]; 
          $filename = explode(".", $_FILES["photo"]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          for($x=0;$x<count($photo_file_types_array);$x++){ 
            if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
            } 
          } 

          if($file_ext_allow){ 
            if($_FILES["photo"]["size"]<$photo_max_file_size){ 
              if(move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_upload_dir.$_POST["id"].$realfiledata)){ 
              
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



      print "<HTML><HEAD>\n";
      if (defined("IN_ADMIN")) {
       if ($utype=='supervisor')
        print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=experts&s=1'>\n";
       else
       if ($utype=='expert')
        print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=experts'>\n";
       else
       if ($utype=='user')  
        print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=members'>\n";
      }
      else 
      if (USER_ID==$_POST["id"])
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=edituser'>\n";
      else
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=welcome'>\n";
      print "</HEAD></HTML>\n";

   } 
   else  
   {
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    puterror("Ошибка при обращении к базе данных");
   }
   
}

if (empty($action)) 
{
  // Извлекаем параметр $id из строки запроса
 if (defined("IN_ADMIN")) { $id = $_GET['id']; }
 
 if (empty($id)) {
   $id = USER_ID;
 }
  
 if ($id!=USER_ID and !defined("IN_ADMIN"))
  $title = "Просмотр личных данных";
 else
  $title = "Изменение личных данных";

 $titlepage=$title;  

  include "topadmin.php";
  
  $start = $_GET['start'];
  $utype = $_GET['utype'];

  $query = "SELECT * FROM users WHERE id='".$id."'";
  $gst = mysql_query($query);
  if ($gst)
   $member = mysql_fetch_array($gst);
  else 
   puterror("Ошибка при обращении к базе данных");

  
?>

<script type="text/javascript">
$(document).ready(function() {
 

});
</script>

<script type="text/javascript"> 
 $(document).ready(function(){
  
   function err() {
        $(".iferror").hide();
        var hasError = false;
 
        var fioVal = $("#fio").val();
        if(fioVal == '') {
            $("#fio").after('<span class="iferror" style="text-align:center;">Необходимо ввести Имя!</span>');
            hasError = true;
        }
        var loginVal = $("#login").val();
        if(loginVal == '') {
            $("#login").after('<span class="iferror" style="text-align:center;">Необходимо ввести логин!</span>');
            hasError = true;
        }
        var emailVal = $("#email").val();
        if(emailVal == '') {
            $("#email").after('<span class="iferror" style="text-align:center;">Необходимо ввести адрес электронной почты!</span>');
            hasError = true;
        }
        var jobVal = $("#job").val();
        if(jobVal == '') {
            $("#job").after('<span class="iferror" style="text-align:center;">Необходимо ввести место работы (учебы)!</span>');
            hasError = true;
        }

        if(hasError == true) { return false; }
    }

     $('#submit1').click(function() {
      if (err()==false) { return false; }
     });
     $('#submit2').click(function() {
      if (err()==false) { return false; }
     });

    $('form').submit(function(){
     $('input[type=submit]', $(this)).attr('disabled', 'disabled');
     $('input[type=button]', $(this)).attr('disabled', 'disabled');
    });   
 });
 function startStatus() {
   $("#spinner").fadeIn("slow");
 }  
 $(function() {
    $( "#tabs" ).tabs();
    $('input[type=submit]').button();
 }); 
</script>

<p align='center'>
<form action="edituser" method="post" enctype="multipart/form-data" onsubmit="startStatus();">
<input type="hidden" name="action" value="post">
<input type="hidden" name="id" value="<?echo $id;?>">
<input type="hidden" name="start" value="<?echo $start;?>">
<input type="hidden" name="utype" value="<?echo $utype;?>">

<div id="tabs">
  <ul>
    <li><a href="#Tab1">Личные данные</a></li>
    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <?} else {?> 
     <li><a href="#Tab2">Приватность</a></li>
    <?}?>
  </ul>
<div id="Tab1">

<table width="100%" border="0" cellpadding=0 cellspacing=0 bordercolordark=white>
<tr><td>
<table width="70%" align='center' border="0" cellpadding=3 cellspacing=3 bordercolordark=white>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Фамилия Имя Отчество:</em></b></p></td>
        <td><p><? echo $member['userfio'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd><b><em class=em>Имя *:</em></b></p></td>
        <td><input id=fio type=text name=fio size=45 value='<? echo $member['userfio'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <?} else {?>
    <tr>
        <td><p class=ptd><b>Логин *:</b></p></td>
        <td><input id=login type=text name=login size=45 value='<? echo $member['username'] ?>'></td>
    </tr>
    <?}?>

    <? if (defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd>Пароль:</p></td>
        <td><input type=text name=pass size=45 maxlength=32></td>
    </tr>
    <?}?>
    
    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Электронная почта:</em></b></p></td>
        <td><p><? echo $member['email'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd><b>Электронная почта *:</b></p></td>
        <td><input id=email type=text name=email size=45 value='<? echo $member['email'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Регион:</em></b></p></td>
        <td><p><? echo $member['region'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd>Регион:</p></td>
        <td><input type=text name=region size=45 value='<? echo $member['region'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Город или населенный пункт:</em></b></p></td>
        <td><p><? echo $member['city'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd>Город или населенный пункт:</p></td>
        <td><input type=text name=city size=45 value='<? echo $member['city'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Место работы (учебы):</em></b></p></td>
        <td><p><? echo $member['job'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd><b>Место работы (учебы)*:</b></p></td>
        <td><input id=job type=text name=job size=45 value='<? echo $member['job'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Должность на месте работы или статус на месте учебы:</em></b></p></td>
        <td><p><? echo $member['person'] ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd>Должность на месте работы или статус на месте учебы:</p></td>
        <td><input type=text name=person size=45 value='<? echo $member['person'] ?>'></td>
    </tr>
    <?}?>

    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd><b><em class=em>Фотография:</em></b></p></td>
        <td><p><? 
        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$member['id'].$member['photoname']."' height='100'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'></div>"; 
        }  
        ?></p></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd>Фотография:</p></td>
        <td><p><? 
        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$member['id'].$member['photoname']."' height='100'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'><div>"; 
        }  
          ?></p>
        <input type="file" name="photo" /><p class=ptd>Размер фотографии не должен превышать 100кб.</p></td>
    </tr>
    <?}?>
    
    <? if (defined("IN_ADMIN")) { ?>
    <tr>
        <td><p class=ptd>Статус:</p></td>
        <td><select name="usertype">
        <? if ($member['usertype']=='expert') { ?> 
         <option value='user'>Участник</option>
         <option value='expert' selected>Эксперт</option>
         <option value='supervisor'>Супервизор</option>
        <? } else if ($member['usertype']=='user') { ?> 
         <option value='user' selected>Участник</option>
         <option value='expert'>Эксперт</option>
         <option value='supervisor'>Супервизор</option>
        <? } else if ($member['usertype']=='supervisor') { ?> 
         <option value='user'>Участник</option>
         <option value='expert'>Эксперт</option>
         <option value='supervisor' selected>Супервизор</option>
        <? } ?>
        </select></td>
    </tr>
    <?} else {?>
    <tr>
        <td><p class=ptd>Статус:</p></td>
        <td>
        <? if ($member['usertype']=='expert') { ?> 
         <p>Эксперт</p>
        <? } else if ($member['usertype']=='user') { ?> 
         <p>Участник</p>
        <? } else if ($member['usertype']=='supervisor') {?>
         <p>Супервизор</p>
        <?}?></td>
    </tr>
    <?}?>
    <? if (defined("IN_ADMIN") and $member['usertype']=='supervisor') { ?>
    <tr>
        <td><p class=ptd>Сколько моделей может создать супервизор:</p></td>
        <td><input type=text name='pacount' size='5' value='<? echo $member['pacount'] ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd>Ограниченный супервизор (1 = Да):</p></td>
        <td><input type=text name='qcount' size='5' value='<? echo $member['qcount'] ?>'></td>
    </tr>
    <?}?>
    
</table>
</td></tr>
<tr><td>
    
    <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
    <p>Дополнительная информация о себе:</p><p><? echo $member['info'] ?></p>
    <?} else {?>
    <table width="70%" align='center' border="0" cellpadding=3 cellspacing=3 bordercolordark=white>
    <tr><td><p class=ptd>Дополнительная информация о себе:</p></td></tr>
    <tr><td><textarea name=info style='width:100%' rows='9'><? echo $member['info'] ?></textarea></td>
    </tr>
    </table>
    </td></tr>
    <?}?>
    <tr><td><p></p></td></tr>
    <tr align='center'>
        <td colspan="3">
            <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
            <?} else {?> 
             <input id=submit1 type="submit" value="Изменить данные">&nbsp;
            <?}?>  
        </td>
    </tr>           
</table>

</div>

<?
    /* echo '<div id="Tab2">';
    
    $res3=mysql_query("SELECT proarrid FROM proexperts WHERE expertid='".$member['id']."' ORDER BY id");
    while($param = mysql_fetch_array($res3))
    {
      $res4=mysql_query("SELECT * FROM projectarray WHERE id='".$param['proarrid']."'");
      $param2 = mysql_fetch_array($res4);
      $res5=mysql_query("SELECT * FROM knowledge WHERE id='".$param2['knowledge_id']."'");
      $param3 = mysql_fetch_array($res5);
      if (!empty($param2['photoname']))
        echo "<p><div class='menu_glide_img'><img src='".$pa_upload_dir.$param2['id'].$param2['photoname']."' height='50'></div>".$param2['name']." (".$param3['name'].")</p>"; 
      else
       echo "<p>".$param2['name']." (".$param3['name'].")</p>";
    } 

    if ($id!=USER_ID and !defined("IN_ADMIN")) { 
    } else {
     echo "<p>Вы можете стать экспертом в новой области знаний:</p>";
     echo "<form method='POST' action='toexpert'>";
     echo "<p><font face='Tahoma,Arial' size='-1'>Введите ключ эксперта:&nbsp;</font></p>
     <input type='hidden' name='userid' value='".$id."'>
     <p><input type='password' name='key' size='7'></p>";
  	 echo "<p><input type='submit' name='ok' value='Стать экспертом'></p>";
     echo "</form>"; 
    }
  echo "</div>"; */  
?>   

<? if ($id!=USER_ID and !defined("IN_ADMIN")) {} else {?> 
<div id="Tab2">

<table border="0" width="100%" cellpadding=0 cellspacing=0 bordercolorlight=gray bordercolordark=white>
<tr><td>
<table width="100%" border="0" cellpadding=3 cellspacing=3>
<tr><td width="200"></td><td><p>Вы можете скрыть поля в Ваших личных данных от просмотра другими участниками системы и незарегистрированными пользователями.</p></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_fio" value="1" <? if ($member['private_fio']==1) echo "checked"; ?>>Скрыть Ф.И.О.</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_email" value="1" <? if ($member['private_email']==1) echo "checked"; ?>>Скрыть Email</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_region" value="1" <? if ($member['private_region']==1) echo "checked"; ?>>Скрыть регион</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_city" value="1" <? if ($member['private_city']==1) echo "checked"; ?>>Скрыть город</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_job" value="1" <? if ($member['private_job']==1) echo "checked"; ?>>Скрыть место работы</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_person" value="1" <? if ($member['private_person']==1) echo "checked"; ?>>Скрыть должность</label></td></tr>
<tr><td width="200"></td><td><label><input type="checkbox" name="private_photo" value="1" <? if ($member['private_photo']==1) echo "checked"; ?>>Скрыть фотографию</label></td></tr>
<tr><td><p></p></td></tr>
</table>

</td>
</tr>
    <tr><td><p></p></td></tr>
    <tr align='center'>
        <td colspan="3">
            <? if ($id!=USER_ID and !defined("IN_ADMIN")) { ?>
            <?} else {?> 
             <input id=submit2 type="submit" value="Изменить данные">&nbsp;
            <?}?>  
        </td>
    </tr>           
</table>
</div>
            <?}?>  
</div>
</form>
</p>
<div id="spinner"></div>
<?
include "bottomadmin.php";
}
?>