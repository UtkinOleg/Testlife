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
if (empty($action)) 
{
$paid = $_GET["paid"];
$id = $_GET["id"];
require_once "header.php"; 
?>
<link rel="stylesheet" href="scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
<link rel="stylesheet" type="text/css" href="scripts/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>
</head>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
</style>
<script>
  $(function() {
    $( "#change" ).button();
  });
</script>

<body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить параметры выборки вопросов из группы</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> При помощи 'слайдера' укажите количество вопросов, которое будет использоваться в тесте. Можно также установить параметр случайной выборки вопросов из группы. Для использованной группы нельзя устанавливать количество вопросов равное нулю.</p>
	</div>
</div>
<p align='center'>
<table width='100%' border="0" cellpadding=3 cellspacing=3 bordercolorlight=white bordercolordark=white>
<tr><td align='center'>
<form action='editgroupintest' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type='hidden' name='id' value='<? echo $id; ?>'>
<table width="100%" border="0" cellpadding=3 cellspacing=0 bordercolorlight=white bordercolordark=white>
    <tr><td>
      <div id="menu_glide" class="menu_glide">
      <table align='center' width='100%' class=bodytable border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr class=tableheaderhide>
              <td align='center' witdh='200'><p class=help>Наименование группы</p></td>
              <td align='center' witdh='30'><p class=help>Балльная стоимость</p></td>
              <td align='center' witdh='30'><p class=help>Время ответа (мин)</p></td>
              <td align='center' witdh='50'><p class=help>Дата создания</p></td>
              <td align='center' witdh='300'><p class=help>Параметры</p></td>
              <td align='center' witdh='50'><p class=help>Случайно</p></td>
          </tr>   
     <?         
  
  
     $td = mysql_query("SELECT * FROM testdata WHERE id='".$id."' LIMIT 1");
     $testdata = mysql_fetch_array($td);

     $totq = mysql_query("SELECT count(*) FROM questions WHERE qgroupid='".$testdata['groupid']."'");
     $totalq = mysql_fetch_array($totq);

     $qg = mysql_query("SELECT * FROM questgroups WHERE id='".$testdata['groupid']."' LIMIT 1");
     $member = mysql_fetch_array($qg);

     echo "<tr>";
     echo "<td width='200'>
     <p class=zag2><a title='Список вопросов' id='listquestions' href='javascript:;'>".$member['name']." <img src='img/b_view.png'></a></p></td>";
     echo "<td align='center'><p class=zag2>".$member['singleball']."</p></td>";
     echo "<td align='center'><p class=zag2>".$member['singletime']."</p></td>";
     echo "<td><p class=zag2>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
     echo "<td width='300' align='center'>";
     echo "<p><div style='margin: 3px;' id='slideru'></div>
     <label for='qg' id='lqg'>Вопросов: ".$testdata['qcount']."</label>
     <input type='hidden' id='qg' name='qg' value=".$testdata['qcount']."/></p>";
     ?>
        <script>
          $(function() {
          $( "#slideru" ).slider({
           range: "min", value: <? echo $testdata['qcount']; ?>, min: 1, max: <? echo $totalq['count(*)']; ?>, step: 1,
           slide: function( event, ui ) {
           $( "#qg" ).val(ui.value);
           $( "#lqg" ).text('Вопросов: ' + ui.value);
           }
          });
          });
     	  $(document).ready(function() {
         $("#rndp").buttonset();
      	 $("#listquestions").click(function() {
	  			$.fancybox.open({
					href : 'listquestions&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 900,
          height : 350,
          fitToView : false,
          autoSize : false,          
					padding : 5
				 });
			  });
        });
       </script>
         
         </td><td> 
         <div id="rndp">
          <? if ($testdata['random']) {?>
          <input type="radio" value='1' id="closed1" name="rndp" checked="checked"><label for="closed1">Да</label>       
          <input type="radio" value='0' id="closed2" name="rndp"><label for="closed2">Нет</label>       
          <?} else {?>
          <input type="radio" value='1' id="closed1" name="rndp"><label for="closed1">Да</label>       
          <input type="radio" value='0' id="closed2" name="rndp" checked="checked"><label for="closed2">Нет</label>       
          <?}?>
         </div> 
         </td>
         <?         

//     echo "</td>";
//     echo "<td align='center'><input type='checkbox' name='rnd' value='".$testdata['random']."'></td>";
//     echo "</tr>";
        ?>
    </table></div>
    </td></tr>    
    <tr align="center"><td>
    <p>
     <input type="submit" id="change" style="font-size: 14px;" value="Изменить"> 
    </p>
    </td></tr>           
</table>
</form>
</td></tr>
</table>
</p></td></tr></table>
</body></html>
<?
  
 
}
else
if (!empty($action)) 
{
 $paid = $_POST["paid"];
 $id = $_POST["id"];
 $questcount = $_POST["qg"];
 $rndp = $_POST["rndp"];
 $query = "UPDATE testdata SET qcount = '".$questcount."'
            , random = '".$rndp."'
           WHERE id=".$id;
 mysql_query($query);
 echo '<script language="javascript">';
 echo 'parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/testoptions&paid='.$paid.'");';
 echo '</script>';
 exit();
}} 
else 
die;
?>
