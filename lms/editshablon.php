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
    $error = $error." Вы не указали группу.";
  }
  if (empty($_POST["iniball"]) and $_POST["kind"]==2) 
  {
    $action = ""; 
    $error = $error." Вы не ввели балл инициазизации составного критерия.";
  }

  $tot22 = mysql_query("SELECT sum(maxball) FROM shablon WHERE id<>'".$_POST["id"]."' AND groupid='".$_POST["groupid"]."'");
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


    $kind = $_POST["kind"];
    
    $complex = 0;
    $digital = 0;
    if ($kind==2) 
     $complex = 1; 
    else
    if ($kind==1) 
     $digital = 1; 
  
  if (!empty($action)) 
  {
     
   mysql_query("LOCK TABLES shablon WRITE");
   mysql_query("SET AUTOCOMMIT = 0");
   $query = "UPDATE shablon SET name = '".$_POST["name"]."'
            , groupid = '".$_POST["groupid"]."' 
            , maxball = '".$_POST["maxball"]."' 
            , info = '".$_POST["info"]."' 
            , complex = '".$complex."' 
            , iniball = '".$_POST["iniball"]."' 
            , digital = '".$digital."' 
           WHERE id=".$_POST["id"];
   
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
  $paid = $_GET['paid'];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

  $id = $_GET['id'];
  $start = $_GET['start'];
  $query = "SELECT * FROM shablon WHERE id = $id";
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
    var comp = <? echo $member['complex']; ?>;
    if (comp === 1)
     { ib.spinner( "enable" ); }
    else
    if (comp === 0)
     { ib.spinner( "disable" ); }
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить параметры критерия</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p>
      <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
     	<div id="content">
      <? 
       if ($member['digital']==0 && $member['complex']==0) 
        echo "Выбор балла - оценка критерия устанавливается при помощи слайдера."; 
       else 
       if ($member['digital']==1)
        echo "Цифровой критерий - оценка производится путем ввода числа.";
       if ($member['complex']==1) 
        echo "Составной критерий позволяет разделить критерий на несколько составных и назначать на каждый отдельный элемент критерия определенный балл либо увеличения, либо уменьшения оценки.";
      ?>
       </div>
    </p>
	</div>
<center>
<form action='editshablon' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type='hidden' name='id' value='<?php echo $id; ?>'>
<div id='menu_glide' class='menu_glide'>
<table align='center' width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width='30%'><p class=ptd><b><em class=em>Наименование критерия *</em></b></td>
        <td width='70%'><input type=text id='name' name='name' style='width:100%' value='<? echo $member['name'] ?>'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Тип критерия *</em></b></td>
        <td>
         <select id='kind' name='kind'>
          <option value='0' <? if ($member['digital']==0 && $member['complex']==0) echo "selected"; ?>>Выбор балла</option>
          <option value='1' <? if ($member['digital']==1) echo "selected"; ?>>Цифровой</option>
          <option value='2' <? if ($member['complex']==1) echo "selected"; ?>>Составной</option></select>
        </td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Группа *</em></b></td>

       <?  
        echo"<td><select id='groupid' name='groupid'>";
        $res4=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$paid."' ORDER BY id");
        while($param4 = mysql_fetch_array($res4))
        {
         if ($member['groupid']==$param4['id']) {  
          echo"<option value='".$param4['id']."' selected>".$param4['name']."</option>";
         } else {
          echo"<option value='".$param4['id']."'>".$param4['name']."</option>";
         } 
        }
        echo"</select></td>";
       ?>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Максимальный балл за критерий *</em></b></td>
        <td><input id='mb' value='<? echo $member['maxball'] ?>' size='5' readonly='1'><input type=hidden name='maxball' id='maxball' value='<? echo $member['maxball'] ?>'></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Пояснение к критерию</em></b></td>
        <td><textarea name='info' style='width:100%' rows='5'><? echo $member['info'] ?></textarea></td>
    </tr><tr>
        <td><p class=ptd><b><em class=em>Балл инициализации составного критерия</em></b></td>
        <td><input id='ib' value='<? echo $member['iniball'] ?>' size='5' readonly='1'><input type=hidden name='iniball' id='iniball' value='<? echo $member['iniball'] ?>'></td>
    </tr>           
</table></div>
<p></p>
            <input id='ok' type="submit" value="Изменить критерий">
</form>
</td></tr></table>
</body></html>

<?
}
}
} else die;
?>