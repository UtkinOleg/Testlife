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
    $yestype = $_POST["yestype"];
    $notype = $_POST["notype"];
    $yesvalue = $_POST["yesvalue"];
    $novalue = $_POST["novalue"];
    
    if ($yesvalue == '') $yesvalue = 0;
    if ($novalue == '') $yesvalue = 0;
    
    // Запрос к базе данных 
    mysql_query("LOCK TABLES shabloncomplex WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    
    $query = "INSERT INTO shabloncomplex VALUES (0,
                                        $paid, $id,
                                        '$name');";
    if(!mysql_query($query))
    {
      exit();
    }

    $shabloncomplexid = mysql_insert_id();
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");

    // Запрос к базе данных 
    $query = "INSERT INTO shabloncparams VALUES (0,
                                        $shabloncomplexid, 'Да',
                                        $yestype, $yesvalue);";
    if(!mysql_query($query))
    {
      exit();
    }

    // Запрос к базе данных 
    $query = "INSERT INTO shabloncparams VALUES (0,
                                        $shabloncomplexid, 'Нет',
                                        $notype, $novalue);";
    if(!mysql_query($query))
    {
      exit();
    }
    
    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/edcomplex&paid=".$paid."&id=".$id."');</script>"; 
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

  $paid = $_GET["paid"];
  $id = $_GET["id"];
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
    var yesb = $( "#yesv" ).spinner({
      min: 0,
      spin: function( event, ui ) {
          $( "#yesvalue" ).val(ui.value);
      }
    });
    var nob = $( "#nov" ).spinner({
      min: 0,
      spin: function( event, ui ) {
          $( "#novalue" ).val(ui.value);
      }
    });
    yesb.spinner( "disable" );
    nob.spinner( "disable" );
    $( "#yestype" ).selectmenu({ 
          width : 150,
          change: function( event, data ) {
           var typecontent = data.item.value;
           if ( typecontent === "0" ) {
            yesb.spinner( "disable" );
           } else if ( typecontent === "1" ) {
            yesb.spinner( "enable" );
           } else if ( typecontent === "-1" ) {
            yesb.spinner( "enable" );
           } 
        }
     });
    $( "#notype" ).selectmenu({ 
          width : 150,
          change: function( event, data ) {
           var typecontent = data.item.value;
           if ( typecontent === "0" ) {
            nob.spinner( "disable" );
           } else if ( typecontent === "1" ) {
            nob.spinner( "enable" );
           } else if ( typecontent === "-1" ) {
            nob.spinner( "enable" );
           } 
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
<table width="100%" border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Новый параметр составного критерия</p></h3>
<form action=addcomp method=post>
<input type=hidden name=id value=<? echo $id; ?>>
<input type=hidden name=paid value=<? echo $paid; ?>>
<input type=hidden name=action value=post>
<p align='center'>
<div id='menu_glide' class='menu_glide'>
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width='50%'><p class=ptd><b><em class=em>Наименование параметра *</em></b></td>
        <td width='50%'><input type='text' id='name' name='name' style='width:100%'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Ответ - "Да"</em></b></td>
       <?  
        echo"<td><select id='yestype' name='yestype'>";
        echo"<option value='0'>Не изменяется</option>";
        echo"<option value='1'>Увеличение</option>";
        echo"<option value='-1'>Уменьшение</option>";
        echo"</select>
        </td></tr>
        <tr><td><p>Величина изменения бальности</p></td><td><input id='yesv' readonly='1' size='5' value='0'><input type='hidden' id='yesvalue' name='yesvalue' value='0'></td>";
       ?>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Ответ - "Нет"</em></b></td>
       <?  
        echo"<td><select id='notype' name='notype'>";
        echo"<option value='0'>Не изменяется</option>";
        echo"<option value='1'>Увеличение</option>";
        echo"<option value='-1'>Уменьшение</option>";
        echo"</select>
        </td></tr>
        <tr><td><p>Величина изменения бальности</p></td><td><input id='nov' readonly='1' size='5' value='0'><input type='hidden' id='novalue' name='novalue' value='0'></td>";
       ?>
    </tr>
</table>
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr align='center'>
        <td>
            <input id='ok' type="submit" value="Добавить"> 
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
