<?php
if(defined("IN_ADMIN")) 
{ 
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$error = "";
$action = "";
$title=$titlepage="Добавить оплату";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  $paid = $_POST["paid"];
  $email = $_POST["email"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {


  if (!empty($email))
  {
 
    
    $gst3 = mysql_query("SELECT * FROM users WHERE email='".$email."' LIMIT 1;");
    if (!$gst3) puterror("Ошибка при обращении к базе данных");
    $user = mysql_fetch_array($gst3);
    $userid = $user['id'];
    $fio = $user['userfio'];
    $usertype = $user['usertype'];  
    mysql_free_result($gst3);

  if ($userid==0)
  {
      echo '<script language="javascript">';
      echo 'alert("Не выбран пользователь.");
      parent.closeFancyboxAndRedirectToUrl("'.$site.'/oplata&paid='.$paid.'");';
      echo '</script>';
      exit();
  }

   $sum = $_POST["sum"];
  if ($sum==0 or empty($sum))
  {
      echo '<script language="javascript">';
      echo 'alert("Не установлена сумма оплаты.");
      parent.closeFancybox();';
      echo '</script>';
      exit();
  }


  if (!empty($userid))
  {

    
    $tm=getdate(time()+9*3600);
    $date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

    mysql_query("LOCK TABLES orders WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "INSERT INTO orders VALUES (0,$userid,1)";
    if(!mysql_query($query)) {
       puterror("Ошибка 1");
    }
    $inv_id = mysql_insert_id();
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    
    
    $query = "INSERT INTO money VALUES (0,
                                        $inv_id,
                                        $paid,
                                        $sum, 
                                        '$date');";
    if(mysql_query($query))
    {
   

        $title = "Оплата услуги по размещению проекта на сайте expert03.ru";

        $body = msghead($fio, $site);
        $body.='<p>Произведена оплата в размере '.$sum.' руб. за размещение проекта <strong>'.$projectarray['name'].'</strong></p>';
        $body.='<p>В личном кабинете можно начинать создание проекта. В зависимости от настроек системы, Вам может быть предложено пройти входное тестирование перед созданием проекта.</p>';
        $body.= msgtail($site);

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
      // Возвращаемся на главную страницу если всё прошло удачно
      echo '<script language="javascript">';
      echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/oplata&paid='.$paid.'");';
      echo '</script>';
      exit();
      
      
    }
    else
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибки при выполнении запроса.");
      parent.closeFancybox();';
      echo '</script>';
      exit();
    }
    
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
<script type="text/javascript">
 $(function(){
   $("#spinner").fadeOut("slow");
   $("#ok").button();
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
            $("#email").focus();
            hasError = true;
     }
     if(hasError == false) {     
     if (checkRegexp(email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i)==0)
     {
            $("#info2").empty();
            $("#info2").append('Адрес электронной почты указан неправильно.');
            $("#email").focus();
            hasError = true;
     }
     }
     if(hasError == false) {     
     var sum = $("#sum");
     if(sum.val()=='') {
            $("#info2").empty();
            $("#info2").append('Укажите сумму оплаты.');
            $("#sum").focus();
            hasError = true;
     }
     }
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
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
          <div id="info2">Укажите адрес электронной почты участника и сумму оплаты.</div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<form action="addoplata" method="post" enctype="multipart/form-data">
<input type=hidden name=action value=post>
<input type=hidden name=paid value=<? echo $paid; ?>>
<div id="menu_glide" class="menu_glide">
<table class=bodytable border="0" width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b>Введите адрес электронной почты:</b></p></td>
    </tr>
    <tr>    
     <td>
        <input type='text' id='email' name='email' style='width:100%;'>
     </td>
    </tr>
    <tr>
        <td><p class=ptd><b>Сумма оплаты (руб.):</b></p></td>
    </tr>
    <tr>    
     <td>
        <input type='text' id='sum' name='sum' style='width:100%;'>
     </td>
    </tr>
</table>
</div>
</td></tr>
    <tr align="center">
        <td>
            <p></p>
            <input id='ok' type="submit" value="Добавить оплату участника">
        </td>
    </tr>           
</form>
</table>
</body></html>
<?
}
} else die;
?>
