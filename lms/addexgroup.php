<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";

$error = "";
$action = "";

$action = $_POST["action"];
if (!empty($action)) 
{
  $paid = $_POST["paid"];
  
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели наименование экспертного листа.";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];

    mysql_query("LOCK TABLES expertcontentnames WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO expertcontentnames VALUES (0,
                                        '$name',
                                        $paid);";


    if(mysql_query($query))
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/shablons&paid=".$paid."');</script>"; 
      exit();
    }
    else
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/shablons&paid=".$paid."');</script>"; 
      exit();
    }

  } 
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/shablons&paid='.$paid.'");';
   echo '</script>';
  } 
  
}
}

if (empty($action)) 
{
  $paid = $_GET["paid"];
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
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
     var name = $("#name");
     if(name.val()=='') {
            name.after('<span class="iferror"><strong>Введите наименование!</strong></span>');
            name.focus();
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
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
<p></p>
<form action='addexgroup' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<div id="menu_glide" class="menu_glide">
<table width='100%' align='center' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Наименование *</em></b></p></td>
        <td><input type=text  id='name' name='name' style='width:100%'></td>
</table>
<p></p>
<input id='ok' type="submit" value="Добавить экспертный лист">
</form>
</table></div>
</td></tr></table>
</body></html>

<?php
  
}

}} else die;
?>
