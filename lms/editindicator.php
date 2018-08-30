<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных

include "config.php";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  
  $paid = $_POST["paid"];
  $id = $_POST["id"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."Вы не ввели наименование показателя.\n";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];
    $oldname = $name;

    $poptionid1 = $_POST["poptionid1"];
    $popt = mysql_query("SELECT name FROM poptions WHERE id='".$poptionid1."' ORDER BY name");
    if (!$popt) puterror("Ошибка при обращении к базе данных");
    $poptmember = mysql_fetch_array($popt);
    $indicator1name = $poptmember['name'];
    
    $poptionid2 = $_POST["poptionid2"];
    $popt = mysql_query("SELECT name FROM poptions WHERE id='".$poptionid2."' ORDER BY name");
    if (!$popt) puterror("Ошибка при обращении к базе данных");
    $poptmember = mysql_fetch_array($popt);
    $indicator2name = $poptmember['name'];

    $operation = $_POST["operation"];

    $query = "UPDATE pindicator SET name = '".$name."'
            , indicator1name = '".$indicator1name."'
            , indicator2name = '".$indicator2name."' 
            , poptionid1 = '".$poptionid1."' 
            , poptionid2 = '".$poptionid2."' 
            , operation = '".$operation."' 
           WHERE id=".$id;
    if(!mysql_query($query))
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибка при выполнении запроса.");
      parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/pindicator&paid='.$paid.'");';
      echo '</script>';
      exit();
    }
   
      // Возвращаемся на главную страницу если всё прошло удачно
      echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/pindicator&paid=".$paid."');</script>"; 
      exit();
    
  }
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/pindicator&paid='.$paid.'");';
   echo '</script>';
   exit();
  } 
    
  
}

}
else
if (empty($action)) 
{
  $id = $_GET['id'];
  $paid = $_GET["paid"];
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {


  $query = "SELECT * FROM pindicator WHERE id=$id and proarrid=$paid LIMIT 1;";
  $gst = mysql_query($query);
  if ($gst) $member = mysql_fetch_array($gst);
  else puterror("Ошибка при обращении к базе данных");

  
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
        $( "#operation" ).selectmenu();
        $( "#poptionid1" ).selectmenu();
        $( "#poptionid2" ).selectmenu();
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить вычисляемый показатель</p></h3>
<form action=editindicator method=post>
<input type=hidden name=action value=post>
<input type=hidden name=id value=<? echo $id; ?>>
<input type=hidden name=paid value=<? echo $paid; ?>>
<div id="menu_glide" class="menu_glide">
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<tr>
        <td witdh='400'><p class=ptd><b><em class=em>Наименование показателя *</em></b></td>
        <td><input type=text id='name' name='name' style='width:100%' value="<?echo $member['name'];?>"></td>
    </tr><tr>
        <td><p class=ptd>Первый оператор</p></td>
        <td>
         <select id='poptionid1' name='poptionid1'>
<?
  $popt = mysql_query("SELECT * FROM poptions WHERE proarrid='".$paid."' ORDER BY name");
  if (!$popt) puterror("Ошибка при обращении к базе данных");
  while($poptmember = mysql_fetch_array($popt))
  {
    if ($member['poptionid1']==$poptmember['id'])
     echo "<option value='".$poptmember['id']."' selected>".$poptmember['name']."</option>";
    else
     echo "<option value='".$poptmember['id']."'>".$poptmember['name']."</option>";
  }
?>
        </select>
    </tr>
    <tr>
        <td><p class=ptd>Операция</p></td>
        <td>
        <select id='operation' name='operation'>
         <option value='mul' <? if ($member['operation']=='mul') echo " selected";?>>Умножение</option>
         <option value='div' <? if ($member['operation']=='div') echo " selected";?>>Деление</option>
         <option value='sum' <? if ($member['operation']=='sum') echo " selected";?>>Сумма</option>
         <option value='sub' <? if ($member['operation']=='sub') echo " selected";?>>Вычитание</option>
        </select>
    </tr><tr>
        <td><p class=ptd>Второй оператор</p></td>
        <td>
         <select id='poptionid2' name='poptionid2'>
<?
  $popt = mysql_query("SELECT * FROM poptions WHERE proarrid='".$paid."' ORDER BY name");
  if (!$popt) puterror("Ошибка при обращении к базе данных");
  while($poptmember = mysql_fetch_array($popt))
  {
    if ($member['poptionid2']==$poptmember['id'])
     echo "<option value='".$poptmember['id']."' selected>".$poptmember['name']."</option>";
    else
     echo "<option value='".$poptmember['id']."'>".$poptmember['name']."</option>";
  }
?>
        </select>
    </tr><tr>
        <td></td><td colspan="3" witdh='400'>
            <input type="submit" value="Изменить">
        </td>
    </tr>                    
</table></div>
</form>
</td></tr></table>
</body></html>
<?
}
}
} else die;
?>
