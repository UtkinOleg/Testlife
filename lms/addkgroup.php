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
  
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели наименование группы критериев.";
  }
  if (empty($_POST["maxball"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели максимальный балл.";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];
    $maxball = $_POST["maxball"];
    $exlistid = $_POST["exlistid"];

    mysql_query("LOCK TABLES shablongroups WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO shablongroups VALUES (0,
                                        '$name',
                                        $maxball,
                                        $paid,
                                        $exlistid);";


    mysql_query($query);
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/shablons&paid=".$paid."&tab=1');</script>"; 
    exit();
  } 
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/shablons&paid='.$paid.'&tab=1");';
   echo '</script>';
   exit();
  } 
  
}
}

if (empty($action)) 
{
  $paid = $_GET["paid"];
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
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
    $( "#exlistid" ).selectmenu();
    $( "#mb" ).spinner({
      min: 1,
      spin: function( event, ui ) {
          $( "#maxball" ).val(ui.value);
      }
    });
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Новая группа критериев</p></h3>
<form action='addkgroup' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<div id="menu_glide" class="menu_glide">
<table width='100%' align='center' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Наименование *</em></b></p></td>
        <td><input type='text' id='name' name='name' style='width:100%'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Максимальный балл *</em></b></p></td>
        <td><input id='mb' size='5' value='1' readonly='1'><input type='hidden' id='maxball' name='maxball' value='1'></td>
    </tr>           
    <tr>
        <td><p class=ptd><b><em class=em>Экспертный лист *</em></b></td>
        <td><select id='exlistid' name='exlistid'>
        <? 
          echo "<option value='0'>По умолчанию</option>";
         $know = mysql_query("SELECT * FROM expertcontentnames WHERE proarrid='".$paid."' ORDER BY id");
          while($knowmember = mysql_fetch_array($know))
          {
            echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
          }
        ?>
        </select></td>
    </tr>

</table></div>
<p align='center'></p>
<input id='ok' type="submit" value="Добавить группу критериев">
</form>
</td></tr></table>
</body></html>

<?php
  
}

}} else die;
?>
