<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
 
 include "config.php";
 include "func.php";

 $title = "Редактирование теста";
 $titlepage=$title;  

 $action = $_POST["action"];

if (!empty($action)) 
{
  $paid = $_POST["paid"];

  $gst3 = mysqli_query($mysqli,"SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);

  $id = $_POST["id"];

  $tg = mysqli_query($mysqli,"SELECT ownerid FROM testgroups WHERE id='".$id."' LIMIT 1;");
  if (!$tg) puterror("Ошибка при обращении к базе данных");
  $tgdata = mysqli_fetch_array($tg);

   if ((defined("IN_SUPERVISOR") and $paid == 0 and $tgdata['ownerid'] == USER_ID) or 
   (defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели наименование.";
  }
  
  if (!empty($action)) 
  {
    
    $maxball = $_POST["maxball"];
    if (empty($maxball)) $maxball=0;
    $testfor = $_POST["testfor"];
    if (empty($testfor)) $testfor='';
    $attempt = $_POST["attempt"];
    if (empty($attempt)) $attempt=0;
    $enable = $_POST["enable"];
    if (empty($enable)) $enable=0;
    
//    $userid = USER_ID;
//    $token = md5(time().$userid.$_POST["name"]);  // Уникальная сигнатура теста

    $query = "UPDATE testgroups SET name = '".$_POST["name"]."'
            , maxball = '".$maxball."'
            , testfor = '".$testfor."' 
            , enable = '".$enable."' 
            , attempt = '".$attempt."' 
           WHERE id=".$_POST["id"];
    mysqli_query($mysqli,$query);
    echo '<script language="javascript">';
    echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/testoptions&paid='.$paid.'");';
    echo '</script>';
    exit();
  }
  else  
  {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибки:'.$error.'");
      parent.closeFancyboxAndRedirectToUrl("'.$site.'/testoptions&paid='.$paid.'");';
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
  

  $gst3 = mysqli_query($mysqli,"SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);

  $tg = mysqli_query($mysqli,"SELECT ownerid FROM testgroups WHERE id='".$id."' LIMIT 1;");
  if (!$tg) puterror("Ошибка при обращении к базе данных");
  $tgdata = mysqli_fetch_array($tg);

  if ((defined("IN_SUPERVISOR") and $paid == 0 and $tgdata['ownerid'] == USER_ID) or 
  (defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {

  $query = "SELECT * FROM testgroups WHERE id = $id LIMIT 1";
  $gst = mysqli_query($mysqli,$query);
  if ($gst) $member = mysqli_fetch_array($gst);
  else puterror("Ошибка при обращении к базе данных");

  
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
label {
    display: inline-block; width: 5em;
  }
  fieldset div {
    margin-bottom: 2em;
  }
  fieldset .help {
    display: inline-block;
  }
  .ui-tooltip {
    width: 210px;
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
    var paid = <? echo $paid?>;
    var tooltips = $( "[title]" ).tooltip({
      position: {
        my: "left top",
        at: "right+5 top-5"
      }
    });
    $( "#change" ).button();
    if (paid==0)
    {
      $( "#enable" ).selectmenu({ width: 70, disabled: true });
      $( "#testfor" ).selectmenu({ disabled: true });
      $( "#attempt" ).selectmenu({ width: 150, disabled: true });
      $( "#slideru" ).slider({
           disabled: true,
           range: "min", value: <? echo $member['maxball'] ?>, min: 0, max: 100, step: 1,
           slide: function( event, ui ) {
           $( "#maxball" ).val(ui.value);
           $( "#mb" ).text(ui.value + '%');
           }
          });
     } 
     else 
     {
      $( "#enable" ).selectmenu({ width: 70 });
      $( "#testfor" ).selectmenu();
      $( "#attempt" ).selectmenu({ width: 150 });
      $( "#slideru" ).slider({
           range: "min", value: <? echo $member['maxball'] ?>, min: 0, max: 100, step: 1,
           slide: function( event, ui ) {
           $( "#maxball" ).val(ui.value);
           $( "#mb" ).text(ui.value + '%');
           }
          });
     }    
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить тест</p></h3>
<p align='center'>
<form action=edittest method=post>
<table  width='100%' border="0" cellpadding=3 cellspacing=3 bordercolordark=white>
<tr><td>
<input type=hidden name=action value=post>
<input type=hidden name=paid value=<? echo $paid; ?>>
<input type=hidden name=id value=<? echo $id; ?>>
<p align='center'>
<div id="menu_glide" class="menu_glide">
<table align="center" class=bodytable width='100%' border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b>Наименование теста *</b></p></td>
        <td><input type=text name=name id=name style='width:100%;' value='<? echo $member['name'] ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Минимальный порог *</em></b></p></td>
        <td align="center"><div style='margin: 3px;' id='slideru' title="Минимальный порог в процентах определяет оценку тестируемого (минимальный балл), необходимый для прохождения теста."></div>
         <label for='maxball' id='mb'><? echo $member['maxball'] ?>%</label>
         <input type='hidden' id='maxball' name='maxball' value="<? echo $member['maxball'] ?>"/>
        </td>
    </tr><tr>
        <td><p class=ptd>Тест используется для</p></td><td><select id="testfor" name="testfor">
        <? if ($member['testfor']=='member') {?>
        <option value='member' selected>участников (для входного контроля)</option>";
        <option value='expert'>экспертов (для оценки уровня знанийЪ</option>";
        <? } else if ($member['testfor']=='expert') {?>
        <option value='member'>участников (для входного контроля)</option>";
        <option value='expert' selected>экспертов (для оценки уровня знаний)</option>";
        <? } ?>
        </select></td>
    </tr><tr>
        <td><p class=ptd>Количество попыток тестирования</p></td><td>
        <select id="attempt" name="attempt">
        <option value='0' <? if ($member['attempt']==0) echo "selected"; ?>>Без ограничений</option>";
        <option value='1' <? if ($member['attempt']==1) echo "selected"; ?>>Одна</option>";
        <option value='2' <? if ($member['attempt']==2) echo "selected"; ?>>Две</option>";
        <option value='3' <? if ($member['attempt']==3) echo "selected"; ?>>Три</option>";
        <option value='5' <? if ($member['attempt']==5) echo "selected"; ?>>Пять</option>";
        </select>
        </td>
    </tr>
    <tr>
        <td><p class=ptd>Включить тест:</p></td>
        <td>
        <select id="enable" name="enable">
         <option value='0' <? if ($member['enable']==0) echo "selected"; ?>>Нет</option>";
         <option value='1' <? if ($member['enable']==1) echo "selected"; ?>>Да</option>";
        </select>
        </td>
    </tr>
</table></div></p>
</td></tr>
    <tr align="center">
        <td>
            <input type="submit" id="change" style="font-size: 14px;" value="Изменить">
        </td>
    </tr>           
</table>
</form>
</p></td></tr>
</table>
</body></html>
<?
}}} else die;
?>