<?php
 if(defined("USER_REGISTERED")) {  
 
 include "config.php";
 include "func.php";

 $action = $_POST["action"];

if (!empty($action)) 
{

  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["title"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели заголовок сообщения.";
  }

  if (empty($_POST["body"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели текст сообщения.";
  }

 if (!empty($action))
 {
  $toid = $_POST["toid"];
  $touser = $_POST["touser"];
  $toemail = $_POST["toemail"];
  $title = "Сообщение на expert03.ru: ".$_POST["title"];
  $body1 = htmlspecialchars($_POST["body"], ENT_QUOTES);
  $fromid = USER_ID;

  $body = msghead($touser, $site);
  $body.='<p>Вам отправлено сообщение от '.USER_FIO.':</p>';
  $body.='<p><strong>'.$body1.'</strong></p>';
  $body.='<p>Ответить на сообщение Вы можете в личном кабинете.</p>';
  $body.= msgtail($site);

  require_once('lib/unicode.inc');

 $mimeheaders = array();
 $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
 $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
 $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
 $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

 if (!empty($toemail))
 {
  
  mail($toemail,mime_header_encode($title),str_replace("\r", '', $body),
  join("\n", $mimeheaders));

  $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        $fromid,
                                        '$title',
                                        '$body1',0,NOW());";
  if(mysql_query($query))
     {
      
      echo "<script>alert('Сообщение отправлено.');parent.closeFancybox();</script>"; 
     }
              
 }
   
 }
 else
 {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancybox();';
   echo '</script>';
 } 
}
else
if (empty($action)) 
{
  $id = $_GET['id'];
  $title = $_GET['title'];
  $content = $_GET['content'];

  $query = "SELECT * FROM users WHERE id = '".$id."' LIMIT 1";
  $gst = mysql_query($query);
  if ($gst)
    $member = mysql_fetch_array($gst);
  else 
    puterror("Ошибка при обращении к базе данных");

  
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
.iferror {
	margin:0;
  color: #FF4565; 
  font-size: 0.7em;
  font-family: Verdana,Arial,sans-serif;
}
.error .iferror {
	display:block;
}
</style>
<script>
  $(function() {
    $( "#ok" ).button();
  });
 $(document).ready(function(){
    $('form').submit(function()
    {
     var hasError = false; 
     $(".iferror").hide();
     var title = $("#title");
     if(title.val()=='') {
            title.after('<span class="iferror"><strong>Введите заголовок!</strong></span>');
            title.focus();
            hasError = true;
     }
     var body = $("#body");
     if(body.val()=='') {
            body.after('<span class="iferror"><strong>Введите сообщение!</strong></span>');
            body.focus();
            hasError = true;
     }
     if(hasError == true) {
       return false; 
     }
     else
     {
       $('input[type=submit]', $(this)).attr('disabled', 'disabled');
       return true; 
     }
    });   
  });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<p align='center'>
<b>Новое сообщение</b></p>
<form action=msg method=post>
<input type=hidden name=action value=post>
<p align='center'>
<div id='menu_glide' class='menu_glide'>
<table width='100%' align="center" class=bodytable border=0 cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><font face="Tahoma,Arial" size=-1>Кому: <? echo $member['userfio'] ?></font></td>
    </tr>
    <tr>
        <td><font face="Tahoma,Arial" size=-1>Заголовок:</font></td>
    </tr>
    <tr>
        <td><input type=text id="title" name="title" style='width:100%' value=<? echo $title; ?>></td>
    </tr>
    <tr>
        <td><font face="Tahoma,Arial" size=-1>Сообщение:</font></td>
    </tr>
    <tr>
        <td><textarea name="body" id="body" style='width:100%' rows="12"><? echo $content; ?></textarea></td>
    </tr>
    <tr align='center'>
        <td colspan="3">
            <input id="ok" type="submit" value="Отправить сообщение">
        </td>
    </tr>           
</table></div></p>
<input type=hidden name=toid value=<?php echo $id; ?>>
<input type=hidden name=toemail value=<?php echo $member['email']; ?>>
<input type=hidden name=touser value=<?php echo $member['userfio']; ?>>
</form>
</td></tr></table>
</body></html>
<?


} 

} else die;
?>