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
    $error = $error." Вы не ввели наименование критерия.";
  }
  if (empty($_POST["maxball"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели максимальный балл.";
  }
  if (empty($_POST["groupid"])) 
  {
    $action = ""; 
    $error = $error." Вы не указали группу критериев.";
  }
  if (empty($_POST["iniball"]) and $_POST["kind"]==2) 
  {
    $action = ""; 
    $error = $error." Вы не ввели балл инициазизации составного критерия.";
  }

  $tot22 = mysql_query("SELECT sum(maxball) FROM shablon WHERE groupid='".$_POST["groupid"]."'");
  $tot22sh = mysql_fetch_array($tot22);
  $countmax = $tot22sh['sum(maxball)'];

  $tot = mysql_query("SELECT maxball FROM shablongroups WHERE id='".$_POST["groupid"]."'");
  $tot2cnt = mysql_fetch_array($tot);
  $countkg = $tot2cnt['maxball'];
  
  if ($countmax + $_POST["maxball"] > $countkg) {
    $action = ""; 
    $res = $countmax + $_POST["maxball"] - $countkg;
    $error = $error." Превышен максимальный балл по данной группе критериев на ".$res." баллов.";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];
    $groupid = $_POST["groupid"];
    $maxball = $_POST["maxball"];
    $info = $_POST["info"];
    $kind = $_POST["kind"];
    
    $complex = 0;
    $digital = 0;
    if ($kind==2) 
     $complex = 1; 
    else
    if ($kind==1) 
     $digital = 1; 
    
    
    $iniball = $_POST["iniball"];
    
    if ($iniball == '') $iniball = 0;

    mysql_query("LOCK TABLES shablon WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO shablon VALUES (0,
                                        '$name',
                                        '$info',
                                        '$groupid',
                                        $maxball,
                                        $paid, 
                                        $complex, 
                                        $iniball, 
                                        $digital);";
    mysql_query($query);
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/shablons&paid=".$paid."&tab=2');</script>"; 
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
  font-size: 0.8em;
  font-family: Verdana,Arial,sans-serif;
}
.error .iferror {
	display:block;
}
</style>
<script>
   $(function() {
    $( "#ok" ).button();
    $( "#groupid" ).selectmenu();
    $( "#mb" ).spinner({
      min: 1,
      spin: function( event, ui ) {
          $( "#maxball" ).val(ui.value);
      }
    });
    var ib = $( "#ib" ).spinner({
      min: 1,
      spin: function( event, ui ) {
          $( "#iniball" ).val(ui.value);
      }
    });
    ib.spinner( "disable" );
    $( document ).tooltip({
      show: {
        effect: "slideDown",
        delay: 250
      },
      position: {
        my: "center top+20",
        at: "center bottom"
      }
    });         
    $( "#kind" ).selectmenu({
          width: 150,
          change: function( event, data ) {
          var str = "";
          var typecontent = data.item.value;
          if ( typecontent === "0" ) {
            str += "Выбор балла - оценка критерия устанавливается при помощи слайдера.";
            ib.spinner( "disable" );
          } else if ( typecontent === "1" ) {
            str += "Цифровой критерий - оценка производится путем ввода числа.";
            ib.spinner( "disable" );
          } else if ( typecontent === "2" ) {
            str += "Составной критерий позволяет разделить критерий на несколько составных и назначать на каждый отдельный элемент критерия определенный балл либо увеличения, либо уменьшения оценки.";
            ib.spinner( "enable" );
          } 
          $( "#content" ).text( str );
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Новый критерий</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p>
      <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
     	<div id="content">Выбор балла - оценка критерия устанавливается при помощи слайдера.</div>
    </p>
	</div>
</div> 
<form action='addshablon' method='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type='hidden' name='action' value='post'>
<div id='menu_glide' class='menu_glide'>
<table align='center' width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width='30%'><p class=ptd><b><em class=em>Наименование критерия *</em></b></td>
        <td width='70%'><input type=text id='name' name='name' style='width:100%'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Тип критерия *</em></b></td>
        <td>
        <select name='kind' id='kind'>
        <option value='0' selected>Выбор балла</option>
        <option value='1'>Цифровой</option>
        <option value='2'>Составной</option>
        </select></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Группа *</em></b></td>

       <?  
        echo"<td><select id=groupid name=groupid>";
        $res4=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$paid."' ORDER BY id");
        while($param4 = mysql_fetch_array($res4))
        {
         echo"<option value='".$param4['id']."'>".$param4['name']."</option>";
        }
        echo"</select></td>";
       ?>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Максимальный балл за критерий *</em></b></td>
        <td><input id='mb' size='5' readonly='1' value='1' title='Максимальный балл устанавливает максимальную оценку за выбранный критерий для типа - Выбор балла'><input type='hidden' id='maxball' value='1' name='maxball'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Пояснение к критерию</em></b></td>
        <td><textarea name=info style='width:100%' rows='5'></textarea></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Балл инициализации составного критерия</em></b></td>
        <td><input id='ib' size='5' readonly='1' value='1' title='Балл инициализации составного критерия указывается только в случае установки праметра - тип Составной'><input type='hidden' id='iniball' value='1' name='iniball'></td>
    </tr>
</table></div>
<p></p>
            <input id='ok' type="submit" value="Добавить критерий">
</form>
</td></tr></table>
</body></html>

<?
}
}
} else die;
?>
