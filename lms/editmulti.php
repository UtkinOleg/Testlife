<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
 
 include "config.php";
 include "func.php";

 $action = $_POST["action"];

 if (!empty($action)) 
 {
  $paid = $_POST["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."Вы не ввели наименование раздела.";
  }

  if (!empty($action)) 
  {
    mysql_query("LOCK TABLES blockcontentnames WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "UPDATE blockcontentnames SET name = '".$_POST["name"]."', info = '".$_POST["info"]."'
           WHERE id=".$_POST["id"];

    mysql_query($query);
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/poptions&paid=".$paid."');</script>"; 
    exit();
  }  
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/poptions&paid='.$paid.'");';
   echo '</script>';
   exit();
  } 
  
}
}

if (empty($action)) 
{
  // Это форма для добавления ответа администратора на сообщение.
  $helppage='';
  // Получаем соединение с базой данных
  // Извлекаем параметр $id из строки запроса
  $paid = $_GET['paid'];
  $id = $_GET['id'];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

  $query = "SELECT * FROM blockcontentnames WHERE id = $id";
  $gst = mysql_query($query);
  if ($gst)
  {
    $member = mysql_fetch_array($gst);
  }
  else puterror("Ошибка при обращении к базе данных");

  
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 1em;
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
    $("#ok").button();
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
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить раздел мультишаблона</p></h3>
<form action='editmulti' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type=hidden name=id value=<?php echo $id; ?>>
<div id="menu_glide" class="menu_glide">
<table class=bodytable width='100%' border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width="200"><p class=ptd><b><em class=em>Наименование раздела*</em></b></p></td>
        <td><input type=text id='name' name='name' style='width:100%' value='<? echo $member['name'] ?>'></td>
    </tr><tr>
        <td witdh='200'><p class=ptd>Информация:</p></td>
        <td><textarea name=info style='width:100%' rows='5'><? echo $member['info'] ?></textarea></td>
    </tr>
</table></div>
<p align='center'>
<input id="ok" type="submit" value="Изменить раздел мультишаблона">
</p
</form>
</td></tr></table>
</body></html>

<?
}
}
} else die;
?>