<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";

$error = "";
$action = "";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  $shid = $_POST["shid"];
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
    $error = $error." Вы не ввели наименование критерия.";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];
    
    $type1 = $_POST["type1"];
    $type2 = $_POST["type2"];
    $value1 = $_POST["value1"];
    $value2 = $_POST["value2"];
    $id1 = $_POST["id1"];
    $id2 = $_POST["id2"];
    
    if ($yesvalue == '') $yesvalue = 0;
    if ($novalue == '') $yesvalue = 0;
    
    $query = "UPDATE shabloncomplex SET name = '".$_POST["name"]."'
           WHERE id=".$_POST["id"];
    
    if(!mysql_query($query))
    {
      exit();
    }

    // Запрос к базе данных 
   $query = "UPDATE shabloncparams SET type = '".$type1."' 
            , value = '".$value1."' 
           WHERE id=".$id1;

    if(!mysql_query($query))
    {
      exit();
    }

    // Запрос к базе данных 
   $query = "UPDATE shabloncparams SET type = '".$type2."' 
            , value = '".$value2."' 
           WHERE id=".$id2;

    if(!mysql_query($query))
    {
      exit();
    }
    
    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/edcomplex&paid=".$paid."&id=".$shid."');</script>"; 
    exit();
  }  
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancybox();';
   echo '</script>';
   exit();
  } 

  
}
}

if (empty($action)) 
{

  $shid = $_GET["shid"];
  $paid = $_GET["paid"];
  $id = $_GET["id"];
  
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  
  $query = "SELECT * FROM shabloncomplex WHERE id='$id'";
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
<table width="100%" border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить параметр составного критерия</p></h3>
<form action=editcomp method=post>
<input type=hidden name=id value=<? echo $id; ?>>
<input type=hidden name=paid value=<? echo $paid; ?>>
<input type=hidden name=shid value=<? echo $shid; ?>>
<input type=hidden name=action value=post>
<p align='center'>
<div id='menu_glide' class='menu_glide'>
<table width='100%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width='50%'><p class=ptd><b><em class=em>Наименование параметра *</em></b></td>
        <td width='50%'><input type='text' id='name' name='name' style='width:100%' value='<? echo $member['name']; ?>'></td>
    </tr>
       <?  

        $gst2 = mysql_query("SELECT * FROM shabloncparams WHERE shabloncid='$id' ORDER BY id");
        if (!$gst2) puterror("Ошибка при обращении к базе данных");
        $c=0;
        while($member2 = mysql_fetch_array($gst2))
           { 
            $c += 1;
            ?>
<script>
   $(function() {
    var b<? echo $c;?> = $( "#val<? echo $c;?>" ).spinner({
      min: 0,
      spin: function( event, ui ) {
          $( "#value<? echo $c;?>" ).val(ui.value);
      }
    });
    <? if ($member2['type'] == 0) 
        echo 'b'.$c.'.spinner( "disable" );';
       else
        echo 'b'.$c.'.spinner( "enable" );';
    ?> 
    $( "#type<? echo $c;?>" ).selectmenu({ 
          width : 150,
          change: function( event, data ) {
           var typecontent = data.item.value;
           if ( typecontent === "0" ) {
            b<? echo $c;?>.spinner( "disable" );
           } else if ( typecontent === "1" ) {
            b<? echo $c;?>.spinner( "enable" );
           } else if ( typecontent === "-1" ) {
            b<? echo $c;?>.spinner( "enable" );
           } 
        }
     });
   }); 
</script>


            <tr><input type=hidden name='id<? echo $c; ?>' value=<? echo $member2['id']; ?>>
            <td><p class=ptd><b><em class=em>Ответ - "<? echo $member2['paramname']; ?>"</em></b></td>
            <td><select name='type<? echo $c; ?>' id='type<? echo $c; ?>'>
            <option <? if ($member2['type'] == 0) echo "selected "; ?>value='0'>Не изменяется</option>
            <option <? if ($member2['type'] == 1) echo "selected "; ?>value='1'>Увеличение</option>
            <option <? if ($member2['type'] == -1) echo "selected "; ?>value='-1'>Уменьшение</option>
            <?
            echo "</select></td></tr>
            <tr><td><p>Величина изменения бальности</p></td><td>
            <input id='val".$c."' size='5' readonly='1' value='".$member2['value']."'><input type='hidden' id='value".$c."' name='value".$c."' value='".$member2['value']."'>
            </td></tr>";
           }

       ?>
</table>
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr align='center'>
        <td>
            <input id='ok' type="submit" value="Изменить"> 
        </td>
    </tr>           
</table>
</div></p></form>
</td></tr></table></body></html>
<?
}
}
} else die;
?>
