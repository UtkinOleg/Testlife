<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$action = "";

$action = $_POST["action"];
if (!empty($action)) 
{
 
  $paid = $_POST["paid"];
 
  $gst3 = mysqli_query($mysqli,"SELECT name, ownerid, checkdate1, checkdate2 FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);
  $name = $projectarray['name'];

if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
{
   
  $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM proexperts WHERE proarrid='".$paid."'");
  $tot2cnt = mysqli_fetch_array($tot2);
  $counti = $tot2cnt['count(*)'];  
  mysqli_free_result($tot2);                         

  $limiter = false;
  if (LOWSUPERVISOR) // Для бесплатного супервизора - один эксперт
   {
     $limiter = true;
     $countallow = 1 - $counti; 
   }
   else
   {
    // Проверим есть ли супервизор в списке ограниченных
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM limitsupervisor WHERE proarrid='".$paid."' AND userid='".USER_ID."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $countlim = $tot2cnt['count(*)'];  
    mysqli_free_result($tot2);                         
    if ($countlim > 0)
    {
     $limiter = true;
     $countallow = 5 - $counti; 
    }
   }
  
  $cntemail = $_POST["cntemails"];
  require_once('lib/unicode.inc');

  mysqli_query($mysqli,"START TRANSACTION;");
  for ($i = 1; $i <= $cntemail; $i++) {
  
  if ($i>$countallow and $limiter) break;
     
  $email = $_POST["email".$i];

  if (!empty($email))
  {
 
   $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM proexperts WHERE proarrid='".$paid."' AND email='".$email."'");
   $tot2cnt = mysqli_fetch_array($tot2);
   $countpr = $tot2cnt['count(*)'];
   mysqli_free_result($tot2);                         
   if ($countpr==0)
   {  
    
    $gst3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE email='".$email."' LIMIT 1;");
    if (!$gst3) puterror("Ошибка при обращении к базе данных");
    $user = mysqli_fetch_array($gst3);
    $userid = $user['id'];
    $fio = $user['userfio'];
    $usertype = $user['usertype'];  
    mysqli_free_result($gst3);
    if (empty($userid)) $userid=0;                        

    if ($usertype=='user' and $userid!=0)
     mysqli_query($mysqli,"UPDATE users SET usertype = 'expert' WHERE id='".$userid."'");
    
    $query = "INSERT INTO proexperts VALUES (0,
                                        $userid,
                                        $paid,
                                        '$email');";
    if(mysqli_query($mysqli,$query))
    {
       $toemail = $email;

       $title = "Приглашение для экспертизы проектов на сайте expert03.ru";

       $body = msghead($fio, $site);
       $body .= '<p>Вам отправлено приглашение от супервизора <strong>'.USER_FIO.'</strong> для экспертизы проектов <strong>'.$name.'</strong> в системе <a href="'.$site.'" title="Экспертная система оценки проектов" target="_blank">'.$site.'</a>.</p>
        <p>Экспертиза проектов начинается '.data_convert ($projectarray['checkdate1'], 1, 0, 0).'.</p>';
       if ($userid!=0)
        $body.='<p>Вам необходимо в срок до '.data_convert ($projectarray['checkdate2'], 1, 0, 0).' в личном кабинете провести экспертизу всех проектов <strong>'.$name.'</strong>.</p>';
       else
        $body.='<p>Вам необходимо в срок до '.data_convert ($projectarray['checkdate2'], 1, 0, 0).' <a href="'.$site.'" title="Экспертная система оценки проектов" target="_blank">зарегистрироваться</a> на данном сайте (кнопка "Вход - Регистрация" или автоматическая регистрация через любую социальную сеть) и провести экспертизу всех проектов <strong>'.$name.'</strong> в личном кабинете.</p>';
       $body.='<p><a href="'.$site.'/page&id=60" title="Инструкция - экспертиза проектов" target="_blank">Подробнее о процедуре экспертизы</a>.</p><p>В зависимости от условий, перед началом экспертизы, для подтверждения статуса <strong>эксперта</strong>, Вам может быть предложено также пройти онлайн тестирование.</p>';
       $body .= msgtail($site);

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
      
      
      
    }
   }
  }
   
  }
  mysqli_query($mysqli,"COMMIT");
  echo '<script language="javascript">';
  echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/proexperts&paid='.$paid.'");';
  echo '</script>';
  exit();
    
 }
}
else
if (empty($action)) 
{
  $paid = $_GET["paid"];
  
require_once "header.php"; 
?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<script type="text/javascript">

 $(function(){
   $("#spinner").fadeOut("slow");
   $("button").button();
 });

 function checkRegexp(n, t) {
        return t.test(n.val())
 }

 function delemail(cnt)
 {
   $("#email"+cnt).val('');
   $('#demail'+cnt).empty();
 }

 function addemail()
 {
     var hasError = false; 
     var cnt = $("#cntemails").val();
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
     
     if(hasError == false) {     
      for (i = 1; i <= cnt; i++) { 
        if ($("#email"+i).val() == email.val())
        {
            $("#info2").empty();
            $("#info2").append('Такой адрес электронной почты уже существует.');
            hasError = true;
            break;
        }
      }
     }
      
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#email").focus();
     }
     else
     {
      cnt++;
      $("#cntemails").val(cnt);
      $('#hiddenemails').append('<input type="hidden" id="email'+cnt+'" name="email'+cnt+'" value="' + email.val() + '">');        
      $('#emails').append('<div id="demail'+cnt+'"><p>' + email.val() + '&nbsp;<button title="Удалить - ' + email.val() + '" id="delb'+cnt+'" onclick="delemail('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      $("#delb"+cnt).button();
      $("#email").val('');
      $("#email").focus();
     }
     
 }
 
 jQuery(document).ready(function() {
    $('#adds').submit(function()
    {
     var cnt = $("#cntemails").val();
     if (cnt>0)
     {
      $('#ok', $(this)).attr('disabled', 'disabled');
      $("#spinner").fadeIn("slow");
      return true;
     } else 
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Укажите адрес эксперта и нажмите кнопку <button id="helpb"><i class="fa fa-plus fa-lg"></i> Добавить эксперта</button>');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#email").focus();
      return false;
     }   
    });   
  });   

</script>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2">Укажите адрес электронной почты эксперта.</div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<div id="buttonsetm">
<div id="menu_glide" class="menu_glide">
<table class="bodytable" border="0" width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b>Введите адрес электронной почты:</b></p></td>
    </tr>
    <tr>    
     <td>
        <form id="adds" action="addsvproexpert" method="post">
        <input type='hidden' name='action' value='post'>
        <input type='hidden' name='paid' value='<? echo $paid; ?>'>
        <input type='text' id='email' name='email' style='width:100%;'>
        <input type='hidden' id='cntemails' name='cntemails' value='0'>
        <div id="hiddenemails"></div>
        </form>
     </td>
    </tr>
    <tr>
     <td>
      <div id="emails"></div>
     </td>
    </tr>
</table>
</div>
</div>
<div id="buttonset">
            <p></p>
            <button id="add" onclick="addemail();"><i class="fa fa-plus fa-lg"></i> Добавить эксперта</button>  
            <button id='ok' onclick="$('#adds').submit();" ><i class="fa fa-newspaper-o fa-lg"></i> Отправить приглашения</button>
            <button id="close" onclick="parent.closeFancybox();"><i class='fa fa-times fa-lg'></i> Отмена</button> 
</div>
</body></html>
<?
} 
} else die; 