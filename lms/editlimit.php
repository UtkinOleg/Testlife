<?php
if(defined("IN_ADMIN")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$action = "";

$action = $_POST["action"];
if (!empty($action)) 
{
 
  $mode = $_POST["mode"];
  $paid = $_POST["paid"];
  $email = $_POST["email"];
 
   
  if (!empty($email))
  {
 
    $gst3 = mysqli_query($mysqli,"SELECT * FROM users WHERE email='".$email."' LIMIT 1;");
    if (!$gst3) puterror("Ошибка при обращении к базе данных");
    $user = mysqli_fetch_array($gst3);
    $userid = $user['id'];
    $fio = $user['userfio'];  
    mysqli_free_result($gst3);
    if (!empty($userid))
    { 
      $gsto = mysqli_query($mysqli,"SELECT name FROM projectarray WHERE id='".$paid."' AND ownerid='".$userid."'");
      if (!$gsto) puterror("Ошибка при обращении к базе данных");
      $pao = mysqli_fetch_array($gsto);
      $name = $pao['name'];
      mysqli_free_result($gsto);
      if (!empty($name))
      { 
                           
     if ($mode=='edit')
     {
      $query = "UPDATE limitsupervisor SET userid = '".$userid."'
            , proarrid = '".$paid."' WHERE id=".$_POST["id"];
     }
     else                                   
     if ($mode=='add')
     {
      $query = "INSERT INTO limitsupervisor VALUES (0,
                                        $userid,
                                        $paid);";
     }
    
    if(mysqli_query($mysqli, $query))
    {
   
       $toemail = $email;
       $title = "Изменение тарифного плана на expert03.ru";
       $body = msghead($fio, $site);
       $body.='<p>Для модели <strong>'.$name.'</strong> изменился тарифный план на <strong>Ограниченный</strong>.</p>';
       $body.='<p>Основные отличия от тарифа <strong>Базовый</strong>:</p>';
       $body.='<p>Максимальное количество проектов в модели - двадцать.</p>';
       $body.='<p>Максимальное количество экспертов - пять.</p>';
       $body .= msgtail($site);

       require_once('lib/unicode.inc');
  
       $mimeheaders = array();
       $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
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
      // Возвращаемся на главную страницу если всё прошло удачно
      echo '<script language="javascript">';
      echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/admlimit");';
      echo '</script>';
      exit();
      
    }  
    }
    }
    else
    {
      echo '<script language="javascript">';
      echo 'alert("Супервизор не найден.");
      parent.closeFancybox();';
      echo '</script>';
      exit();
    }
    
  } 
}
else
if (empty($action)) 
{
 $mode = $_GET['mode'];
 if ($mode=='edit')
 {
  $modename = "Изменить данные супервизора";
  $id = $_GET['id'];
  $query = "SELECT * FROM limitsupervisor WHERE id='".$id."'";
  $gst = mysqli_query($mysqli,$query);
  if ($gst)
   $member = mysqli_fetch_array($gst);
  else 
   puterror("Ошибка при обращении к базе данных");
 }
 else
  $modename = "Добавить данные супервизора";

 require_once "header.php"; 
?>
<script type="text/javascript">
 $(function(){
   $("#spinner").fadeOut("slow");
   $("#ok").button();
   $("#paid").selectmenu();
 });
 $(document).ready(function(){
 
    function checkRegexp(n, t) {
        return t.test(n.val())
    }

    $('form').submit(function()
    {
     var hasError = false; 
     var email = $("#email");
     if(email.val()=='') {
            $("#info2").empty();
            $("#info2").append('Укажите адрес электронной почты.');
            hasError = true;
     }
     if(hasError == false) {     
     if (checkRegexp(email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i)==0)
     {
            $("#info2").empty();
            $("#info2").append('Адрес электронной почты указан неправильно.');
            hasError = true;
     }
     }
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#email").focus();
       return false; 
     }
     $('#ok', $(this)).attr('disabled', 'disabled');
     $("#spinner").fadeIn("slow");
    });   
  });

</script>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2">Укажите адрес электронной почты ограниченного супервизора.</div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<form action="editlimit" method="post" enctype="multipart/form-data">
<input type='hidden' name='action' value='post'>
<input type='hidden' name='id' value='<? echo $id; ?>'>
<input type='hidden' name='mode' value='<? echo $mode; ?>'>
<div id="menu_glide" class="menu_glide">
<table class=bodytable border="0" width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<?
 if ($mode=='edit')
 {
     echo "<tr><td><p>";
     $gst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."'");
     $user = mysqli_fetch_array($gst2);
     if (!empty($user['photoname'])) 
        {
          if (stristr($user['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$user['id'].$user['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$user['photoname']."' height='40'><div>"; 
        }  
     echo "&nbsp;".$user['userfio']." (".$user['email'].")</p></td></tr>"; 
     mysqli_free_result($qst2);
}
?>
    <tr>
        <td><p class=ptd><b>Введите адрес электронной почты:</b></p></td>
    </tr>
    <tr>    
     <td>
        <input type='text' id='email' name='email' style='width:100%;'>
     </td>
    </tr>
    <tr>
        <td><p class=ptd>Модель</p></td></tr>
        <tr><td><select id="paid" name="paid">
        <? 
          if ($member['paid']==0)
            echo "<option selected value='0'>Нет</option>";

          $know = mysqli_query($mysqli,"SELECT * FROM projectarray ORDER BY name;");
          while($knowmember = mysqli_fetch_array($know))
            {
             if ($member['proarrid']==$knowmember['id'])
              echo "<option selected value='".$knowmember['id']."'>".$knowmember['name']."</option>";
             else 
              echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
            }
          mysqli_free_result($know);  
        ?>
        </select></td>
    </tr>    
</table>
</div>
</td></tr>
    <tr align="center">
        <td>
            <p></p>
            <input id='ok' type="submit" value="Добавить супервизора">
        </td>
    </tr>           
</form>
</table>
</body></html>
<?

} 
} else die; 