<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";


$error = "";
$action = "";
// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{

  $testid = $_POST["tid"];
  $begindate = $_POST["bdate"];
  $DateTime1 = DateTime::createFromFormat('d.m.Y', $begindate);
  $begindate = $DateTime1->format('Y-m-d');
  $enddate = $_POST["edate"];
  $DateTime2 = DateTime::createFromFormat('d.m.Y', $enddate);
  $enddate = $DateTime2->format('Y-m-d');
  $userid = $_POST["uid"];

  echo '<script language="javascript">';
  echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/viewtestresults&tid='.$testid.'&bdate='.$begindate.'&edate='.$enddate.'&uid='.$userid.'");';
  echo '</script>';
  exit();

}
else
if (empty($action)) 
{
  $testid = $_GET["tid"];
  $begindate = $_GET["bdate"];
  $enddate = $_GET["edate"];
  $userid = $_GET["uid"];

require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script>
    $(function() {
    
$.datepicker.regional['ru'] = { 
closeText: 'Закрыть', 
prevText: '&#x3c;Пред', 
nextText: 'След&#x3e;', 
currentText: 'Сегодня', 
monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
'Июл','Авг','Сен','Окт','Ноя','Дек'], 
dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], 
dateFormat: 'dd.mm.yy', 
firstDay: 1, 
isRTL: false 
}; 
        $.datepicker.setDefaults($.datepicker.regional['ru']);
    
        $( "#ok" ).button();
        $( "#tid" ).selectmenu();
        $( "#uid" ).selectmenu();
        $( "#bdate" ).datepicker({dateFormat: "dd.mm.yy"});
        $( "#edate" ).datepicker({dateFormat: "dd.mm.yy"});
    });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Поиск результатов тестирования</p></h3><center>
<form action='searchresult' method='post'>
<input type='hidden' name='action' value='post'>
<table border="0" width='100%' height='100%' cellpadding=3 cellspacing=3>
<tr><td>
<p align='center'>
<div id="menu_glide" class="menu_glide">
<table width='100%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p>Наименование теста:</p></td><td><select style="width:100%" id="tid" name="tid"><option></option>
        <? 
          if(defined("IN_ADMIN"))
           $test = mysqli_query($mysqli,"SELECT DISTINCT(s.testid) FROM singleresult as s ORDER BY s.id DESC;");
          else
           $test = mysqli_query($mysqli,"SELECT DISTINCT(s.testid) FROM singleresult as s, testgroups as t WHERE t.id=s.testid AND t.ownerid=".USER_ID." ORDER BY s.id DESC;");
          while($testdata = mysqli_fetch_array($test))
            {
             $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM testgroups WHERE id='".$testdata['testid']."' LIMIT 1;");
             $fromuser = mysqli_fetch_array($from);
             $testname = $fromuser['name'];
             mysqli_free_result($from);
             if ($testdata['testid']==$testid)
              echo "<option selected value='".$testdata['testid']."'>".$testname."</option>";
             else 
              echo "<option value='".$testdata['testid']."'>".$testname."</option>";
            }
          mysqli_free_result($test); 
        ?>
        </select></td>
    </tr>    
    <tr>
        <td><p>Имя:</p></td><td><select style="width:100%" id="uid" name="uid"><option></option>
        <? 
          if(defined("IN_ADMIN"))
           $test = mysqli_query($mysqli,"SELECT DISTINCT(s.userid) FROM singleresult as s ORDER BY s.id;");
          else
           $test = mysqli_query($mysqli,"SELECT DISTINCT(s.userid) FROM singleresult as s, testgroups as t WHERE t.id=s.testid AND t.ownerid=".USER_ID." ORDER BY s.id;");
          while($testdata = mysqli_fetch_array($test))
            {
             $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio, email FROM users WHERE id='".$testdata['userid']."' LIMIT 1;");
             $fromuser = mysqli_fetch_array($from);
             $userfio = $fromuser['userfio'];
             $useremail = $fromuser['email'];
             mysqli_free_result($from);
             if ($testdata['userid']==$userid)
              echo "<option selected value='".$testdata['userid']."'>".$userfio." (".$useremail.") </option>";
             else 
              echo "<option value='".$testdata['userid']."'>".$userfio." (".$useremail.") </option>";
            }
          mysqli_free_result($test); 
        ?>
        </select></td>
    </tr>    
    <tr>
        <td><p>Даты начала и окончания тестирования:</p></td>
        <td>
         <? if (empty($begindate)) {
          $res = mysqli_query($mysqli,"SELECT min(resdate) FROM singleresult LIMIT 1;");
          $testdata = mysqli_fetch_array($res);
         ?>
          <input type='text' id="bdate" name="bdate" value='<? echo data_convert ($testdata['min(resdate)'], 1, 0, 0); ?>'>
         <?
          mysqli_free_result($res); 
         } else {?>
          <input type='text' id="bdate" name="bdate" value='<? echo data_convert ($begindate, 1, 0, 0); ?>'>
         <?}?>

         <? if (empty($enddate)) {
          $res = mysqli_query($mysqli,"SELECT max(resdate) FROM singleresult LIMIT 1;");
          $testdata = mysqli_fetch_array($res);
         ?>
          <input type='text' id="edate" name="edate" value='<? echo data_convert ($testdata['max(resdate)'], 1, 0, 0); ?>'>
         <?
          mysqli_free_result($res); 
         } else {?>
          <input type='text' id="edate" name="edate" value='<? echo data_convert ($enddate, 1, 0, 0); ?>'>
         <?}?>
        </td>
    </tr>

</table>
</div></p>
</td></tr>
    <tr align="center">
        <td>
            <input id="ok" type="submit" value="Поиск результатов"> 
        </td>
    </tr>           
</table>
</form>
</td></tr></table>
</body></html>
<?
}} else die;
?>
