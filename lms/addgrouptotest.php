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

  if ($action=='post') 
  {

  $paid = $_POST["paid"];
  $tid = $_POST["testid"];
  $knowsid = $_POST["knowsid"];
  // Проверяем правильность ввода информации в поля формы
  if (empty($knowsid)) 
  {
    $action = ""; 
    $error = $error." Вы не указали область знаний.";
  }

  if (!empty($action)) 
  {

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
font-size: 0.8em;
}
</style>
<script>
      $(function() {
          $( "#next" ).button();
      });
</script>
<body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Добавить группу вопросов в тест</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		При помощи 'слайдера' укажите количество вопросов, которое будет использоваться в тесте. Можно также установить параметр случайной выборки вопросов из группы. Вопросы из группы не будут использоваться (добавляться в тест), если установленное количество равно нулю.</p>
	</div>
</div>
<p align='center'>
<table borber="0" width="100%">

<? 
  if (defined("IN_ADMIN")) {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE knowsid='".$knowsid."'");
   $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE knowsid='".$knowsid."' ORDER BY id");
  }
  else
  {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowsid."' ORDER BY id");
   $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowsid."' ORDER BY id");
  }
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");

    $total = mysqli_fetch_array($tot);
    if ($total['count(*)']>0)
    {

?>


<tr><td align='center'>
<form action='addgrouptotest' method='post'>
<input type='hidden' name='action' value='post2'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type='hidden' name='testid' value='<? echo $tid; ?>'>
<input type='hidden' name='knowsid' value='<? echo $knowsid; ?>'>
<table border="0" cellpadding=3 cellspacing=3>
    <tr><td>
     <div id="menu_glide" class="menu_glide">
      <table align='center' class=bodytable border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr class=tableheaderhide>
              <td align='center' witdh='30'><p class=help>№</p></td>
              <td align='center' witdh='200'><p class=help>Наименование группы</p></td>
              <td align='center'><p class=help>Балльная стоимость</p></td>
              <td align='center'><p class=help>Время ответа (мин)</p></td>
              <td align='center'><p class=help>Дата создания</p></td>
              <td align='center' witdh='300'><p class=help>Параметры</p></td>
              <td align='center' witdh='100'><p class=help>Случайно</p></td>
          </tr>   
     <?         
  
  $i=0;
  while($member = mysqli_fetch_array($gst))
  {
  
    $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata WHERE groupid='".$member['id']."' AND testid='".$tid."' LIMIT 1;");
    $totaltd = mysqli_fetch_array($td);
    $grintest = $totaltd['count(*)'];
    mysqli_free_result($td);

    $totq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='".$member['id']."'");
    $total = mysqli_fetch_array($totq);
    if ($total['count(*)']>0 and $grintest==0)
    {
     echo "<tr><td witdh='30'><p>".++$i."</p></td>";
     echo "<td width='200'>
     <p class=zag2><a title='Посмотреть список вопросов' id='listquestions".$member['id']."' href='javascript:;'>".$member['name']."</a></p></td>";
     echo "<td align='center'><p class=zag2>".$member['singleball']."</p></td>";
     echo "<td align='center'><p class=zag2>".$member['singletime']."</p></td>";
     echo "<td><p class=zag2>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
     echo "<td width='300' align='center'>";
     echo "<p><div style='margin: 3px;' id='slideru".$member['id']."'></div>
     <label for='qg".$member['id']."' id='lqg".$member['id']."'>Вопросов: 0</label>
     <input type='hidden' id='qg".$member['id']."' name='qg".$member['id']."'/></p>";
     ?>
       <script>
        $(function() {
          $( "#slideru<? echo $member['id'];?>" ).slider({
           range: "min", value:0, min: 0, max: <? echo $total['count(*)']; ?>, step: 1,
           slide: function( event, ui ) {
           $( "#qg<? echo $member['id'];?>" ).val(ui.value);
           $( "#lqg<? echo $member['id'];?>" ).text('Вопросов: ' + ui.value);
           }
          });
        });
    	  $(document).ready(function() {
         $("#rndp<?php echo $member['id']; ?>").buttonset();
      	 $("#listquestions<?php echo $member['id']; ?>").click(function() {
	  			$.fancybox.open({
					href : 'listquestions&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 900,
          height : 450,
          fitToView : false,
          autoSize : false,          
					padding : 5
				 });
			  });
        });
     </script>

         </td><td>
         <div id="rndp<?echo $member['id'];?>">
          <input type="radio" value='1' id="closed1_<?echo $member['id'];?>" name="rnd<?echo $member['id'];?>" checked="checked"><label for="closed1_<?echo $member['id'];?>">Да</label>       
          <input type="radio" value='0' id="closed2_<?echo $member['id'];?>" name="rnd<?echo $member['id'];?>"><label for="closed2_<?echo $member['id'];?>">Нет</label>       
         </div>
         </td>
         <?         

    }
    mysqli_free_result($totq);
   }
        ?>
    </table></div></td></tr>    
    <tr align="center">
        <td>
        </td>
    </tr>           
    <tr align="center">
        <td>
            <p></p>
            <input id="next" type="submit" value="Продолжить"> 
        </td>
    </tr>           
</table>
</form>
</td></tr>

<? } ?>

</table>
</p></td></tr></table>
</body></html>
<?
  
  }
  else
  {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибки:'.$error.'");
      parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/testoptions&paid='.$paid.'");';
      echo '</script>';
      exit();
  }  
  
  }
  else
  if ($action=='post2') 
  {

 $paid = $_POST["paid"];
 $testid = $_POST["testid"];
 $knowsid = $_POST["knowsid"];

 if (defined("IN_ADMIN")) {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE knowsid='".$knowsid."'");
   $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE knowsid='".$knowsid."' ORDER BY id");
  }
 else
  {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowsid."' ORDER BY id");
   $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowsid."' ORDER BY id");
  }
 if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");

 $total = mysqli_fetch_array($tot);
 if ($total['count(*)']>0)
 {
    
  while($member = mysqli_fetch_array($gst))
  {
  
    $totq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='".$member['id']."'");
    $totalq = mysqli_fetch_array($totq);
    if ($totalq['count(*)']>0)
    {
     $questcount = $_POST["qg".$member['id']];
     $rnd = $_POST["rnd".$member['id']];
     $qid = $member['id'];
     if ($questcount>0)
     {
      $query = "INSERT INTO testdata VALUES (0,
                                        $testid,
                                        $questcount,
                                        $rnd, 
                                        $qid);";
      mysqli_query($mysqli,$query);
     }
    }
    mysqli_free_result($totq);
  } 
 }
 mysqli_free_result($gst); 

 echo '<script language="javascript">';
 echo 'parent.closeFancyboxAndRedirectToUrl("http://expert03.ru/testoptions&paid='.$paid.'");';
 echo '</script>';
 exit();
   
 }

}
else
if (empty($action)) 
{

$paid = $_GET['paid'];
$tid = $_GET['tid'];
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
          $( "#knowsid" ).selectmenu();
          $( "#next" ).button();
      });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Добавить группу вопросов в тест</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		Все тесты, создаваемые в системе, как и группы вопросов, принадлежат к определенной области знаний. Тест может принадлежать и к нескольким областям, если в нем находятся группы вопросов из разных областей знаний. Укажите используемую область знаний для теста. В скобках указано количество доступных групп вопросов в области.</p>
	</div>
</div>
<?
          $qc=0;
          $know = mysqli_query($mysqli,"SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY name");
          while($knowmember = mysqli_fetch_array($know))
           {
 
  if (defined("IN_ADMIN")) 
   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$knowmember['id']."'");
  else
   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowmember['id']."'");
  if (!$tot) 
    puterror("Ошибка при обращении к базе данных");
  $total = mysqli_fetch_array($tot);
  $qc += $total['count(*)'];

            mysqli_free_result($tot);
           }
           mysqli_free_result($know);
           
           if ($qc==0)
           {
           ?>
<p></p>
            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Не созданы области знаний или созданные группы вопросов им не принадлежат.</p>
            	</div>
            </div> 
           <?
           } else { ?>
<p align='center'>
<table border=0 cellpadding=0 cellspacing=0 width='100%' bgcolor='#ffffff'><tr><td align="center">
<tr><td>
<form action='addgrouptotest' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='paid' value='<? echo $paid; ?>'>
<input type='hidden' name='testid' value='<? echo $tid; ?>'>
<div id="menu_glide" class="menu_glide">
 <table align='center' width='90%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd>Укажите область знаний:</p></td><td><select style="width:600px;" name="knowsid" id="knowsid" title="Область знаний для группы">
        <? 
          $know = mysqli_query($mysqli,"SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY name");
          while($knowmember = mysqli_fetch_array($know))
           {
 
  if (defined("IN_ADMIN")) 
   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$knowmember['id']."'");
  else
   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' AND knowsid='".$knowmember['id']."'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysqli_fetch_array($tot);
  $qcnt = $total['count(*)'];

            if ($qcnt>0)
             echo "<option value='".$knowmember['id']."'>".$knowmember['name']." (".$qcnt.")</option>";
            mysqli_free_result($tot);
           }
           mysqli_free_result($know);
        ?>
        </select></td>
    </tr>    
</table></div></td></tr>
    <tr align="center">
        <td colspan="3">
            <p></p>
            <input id="next" type="submit" value="Продолжить"> 
        </td>
    </tr>           
</table></form></td></tr></table></p>
<?}?>
</body></html>
<?
}} else die;
?>
