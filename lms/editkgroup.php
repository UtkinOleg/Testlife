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
    $error = $error." Вы не ввели наименование группы критериев.";
  }
  if (empty($_POST["maxball"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели максимальный балл.";
  }

  $tot4 = mysql_query("SELECT SUM(maxball) FROM shablon WHERE groupid='".$_POST["id"]."'");
  $sum = mysql_result($tot4, 0);
  
  if ($_POST["maxball"] < $sum) {
    $action = ""; 
    $res = $sum - $_POST["maxball"];
    $error = $error." Максимальный балл по данной группе критериев на ".$res." баллов меньше суммы максимальных баллов по каждому критерию. Проверьте параметры (максимальный балл) в каждом критерии или увеличьте сумму по группе.";
  }

  if (!empty($action)) 
  {
    mysql_query("LOCK TABLES shablongroups WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "UPDATE shablongroups SET name = '".$_POST["name"]."'
            , maxball = '".$_POST["maxball"]."' 
            , exlistid = '".$_POST["exlistid"]."' 
           WHERE id=".$_POST["id"];
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

  $query = "SELECT * FROM shablongroups WHERE id = $id";
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить группу критериев</p></h3>
<form action='editkgroup' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type=hidden name=id value=<?php echo $id; ?>>
<div id="menu_glide" class="menu_glide">
<table width='100%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Наименование *</em></b></p></td>
        <td><input type=text id='name' name='name' style='width:100%' value='<? echo $member['name'] ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b>Максимальный балл *</b></p></td>
        <td><input id='mb' size='5' readonly='1' value='<? echo $member['maxball'] ?>'><input type='hidden' id='maxball' name='maxball' value='<? echo $member['maxball'] ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Экспертный лист *</em></b></td>
        <td><select name='exlistid' id='exlistid'>
        <? 
         if ($member['exlistid']==0)
          echo "<option value='0' selected>По умолчанию</option>";
         else  
          echo "<option value='0'>По умолчанию</option>";
          $know = mysql_query("SELECT * FROM expertcontentnames WHERE proarrid='".$paid."' ORDER BY id");
           while($knowmember = mysql_fetch_array($know))
           {
            if ($member['exlistid']==$knowmember['id'])
             echo "<option value='".$knowmember['id']."' selected>".$knowmember['name']."</option>";
            else
             echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
           }
          
        ?>
        </select></td>
    </tr>
</table></div>
<p></p>
<input id='ok' type="submit" value="Изменить группу критериев">
</form>
</td></tr></table>
</body></html>

<?
}
}
} else die;
?>