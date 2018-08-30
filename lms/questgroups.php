<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Список групп вопросов";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $kid = $_GET["kid"];

?>
<script type="text/javascript">

	$(document).ready(function() {
			$('.fancybox').fancybox();
    	
      $("#addquestgroup").click(function() {
				$.fancybox.open({
					href : 'addquestgroup&kid=<? echo $kid; ?>',
					type : 'iframe',
          width : 700,
          height : 500,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

  $(".fa").mouseover(function (e) {
      $(this).toggleClass('fa-spin');
  }).mouseout(function (e) {
      $(this).toggleClass('fa-spin');
  }); 
  
	});
  
  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
</script>    

<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Тесты в системе состоят из одной или нескольких групп вопросов. В отличие от тестов, группы вопросов не привязаны к модели. То есть, накопив определенный банк вопрсоов, ими можно пользоваться в разных моделях, формируя тесты из различных комбинаций групп вопросов. В группы помещаются вопросы, которые можно объединить в одну тему и соотнести с определенной областью знаний. Вопросы в группу можно загружать в двух форматах: LMS Moodle XML (подробнее <a href="page&id=55" target="_blank">здесь</a> и в <a href="https://docs.moodle.org/23/en/Moodle_XML_format" target="_blank">документации Moodle</a>) и TXT (в формате ?+-= <a href="page&id=54" target="_blank">подробнее</a>).</p>
	</div>
</div>

<script>
$(function() {
    $("#addquestgroup").button();
    $( "#kid" ).selectmenu({
      width: 350,
      change: function( event, data ) {
        location.replace('questgroups&kid=' + data.item.value);
      }      
     });
  });  
</script>
  <?

  if (defined("IN_ADMIN")) {
   if (empty($kid))
    $gst = mysqli_query($mysqli,"SELECT * FROM questgroups ORDER BY id DESC;");
   else
    $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE knowsid='".$kid."' ORDER BY id DESC;");
  }
  else
  if (defined("IN_SUPERVISOR"))
  {
   if (empty($kid))
    $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE userid='".USER_ID."' ORDER BY id DESC;");
   else
    $gst = mysqli_query($mysqli,"SELECT * FROM questgroups WHERE knowsid='".$kid."' AND userid='".USER_ID."' ORDER BY id DESC;");
  }
  
  if (!$gst) 
   puterror("Ошибка при обращении к базе данных");

  echo "<p align='center'>";

  if (defined("IN_ADMIN")) {
     $ctotalqg = 1;
     echo "<a style='font-size:1em;' id='addquestgroup' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;Добавить новую группу вопросов</a>
     ";
  }
  else
  if (defined("IN_SUPERVISOR"))
  {
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' ORDER BY id");
    $totalqg = mysqli_fetch_array($tot);
    $ctotalqg = $totalqg['count(*)'];
    if (LOWSUPERVISOR)
    {
     if ($totalqg['count(*)']==0) {
     echo "<a style='font-size:1em;' id='addquestgroup' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;Добавить новую группу вопросов</a>
     ";
     }
    }
    else
     echo "<a style='font-size:1em;' id='addquestgroup' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;Добавить новую группу вопросов</a>
     ";
    mysqli_free_result($tot);
  }

  if ($ctotalqg>0)
  {

    if (defined("IN_ADMIN")) 
     $res1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge ORDER BY id;");
    else
     $res1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY id;");
    
    echo"</p><p align='center' style='font-size: 100%;' ><select id='kid' name='kid'>";
    echo"<option value=''>Все области знаний</option>";

    while($r1 = mysqli_fetch_array($res1))
    {
      if ($r1['id']==$_GET["kid"]) 
       echo"<option value='".$r1['id']."' selected>".$r1['name']."</option>";
      else
       echo"<option value='".$r1['id']."'>".$r1['name']."</option>";
    }
    echo"</select>";
    mysqli_free_result($res1); 


  echo "</p>";

  $tableheader = "class=tableheaderhide";
    
    ?>
    <p align='center'>
     <div id='menu_glide' class='menu_glide'>
      <table align='center' width='100%' style="background-color: #FFFFFF;" class='bodytable' border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td align='center' witdh='100'><p class=help>№</p></td>
              <td align='center' witdh='300'><p class=help>Наименование группы</p></td>
              <? if (empty($kid)) {?>
              <td align='center' witdh='300'><p class=help>Область знаний</p></td>
              <?}?>
              <td align='center' witdh='100'><p class=help>Балльная стоимость</p></td>
              <td align='center' witdh='100'><p class=help>Время ответа (мин)</p></td>
              <td align='center' witdh='50'><p class=help></p></td>
              <td align='center' witdh='400'><p class=help>Вопросы</p></td>
              <td align='center' witdh='100'><p class=help>Дата создания</p></td>
              <? if (defined("IN_ADMIN")) {?><td align='center' witdh='100'><p class=help>Автор вопросов</p></td><?}?>
          </tr>   
     <?         

  $i=0;
  while($member = mysqli_fetch_array($gst))
  {

    $tottd = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata WHERE groupid='".$member['id']."'");
    $totaltd = mysqli_fetch_array($tottd);
  
    $totq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='".$member['id']."'");
    $total = mysqli_fetch_array($totq);
  
    echo "<tr><td witdh='100'><p>".++$i."</p></td>";
    echo "<td width='300'>
    <p>".$member['name']." <a id='editquestgroup".$member['id']."' href='javascript:;' title='Редактировать группу вопросов'><i class='fa fa-cog fa-lg'></i></a>";
    if ($total['count(*)']==0) {?>
    &nbsp;<a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,0,'delquestgroup&kid=<? echo $kid; ?>','questgroups','группа')" title="Удалить группу вопросов"><i class='fa fa-trash fa-lg'></i></a>
    <?}?></p><?
    if (!empty($member['comment'])) echo "<p><font face='Tahoma, Arial' size='-2'>".$member['comment']."</font></p>";
    echo "</td>";
    
    if (empty($kid))
    {
    $query4 = "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM knowledge WHERE id = ".$member['knowsid']." LIMIT 1";
    $gst4 = mysqli_query($mysqli,$query4);
    if ($gst4) 
     {
      $member4 = mysqli_fetch_array($gst4);
      echo "<td align='center'><p class=zag2>".$member4['name']."</p></td>";
     }
     else 
      echo "<td align='center'></td>";
    mysqli_free_result($gst4);
    }
    
    echo "<td align='center'><p class=zag2>".$member['singleball']."</p>";
    echo "</td><td align='center'><p class=zag2>".$member['singletime']."</p>";
 
 
    ?>
    
<script type="text/javascript">

		$(document).ready(function() {

    	$("#editquestgroup<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editquestgroup&kid=<? echo $kid; ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 700,
          height : 520,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#addquestfromfile<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'addquestfromfile&kid=<? echo $kid; ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 700,
          height : 210,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#listquestions<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'listquestions&kid=<? echo $kid; ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 1000,
          height : 550,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

    </td>
    <td>
    <? if ($totaltd['count(*)']==0) {?>
    <? if (LOWSUPERVISOR and $total['count(*)']<5) {?>
      <p align="center"><a id="addquestfromfile<? echo $member['id']; ?>" href="javascript:;"><i class='fa fa-plus-square fa-lg' title='Добавить вопросы в группу'></i></a></p>
    <?} else {?>
    <? if (!LOWSUPERVISOR) {?>
      <p align="center"><a id="addquestfromfile<? echo $member['id']; ?>" href="javascript:;"><i class='fa fa-plus-square fa-lg' title='Добавить вопросы в группу'></i></a></p>
    <?}?>
    <?}?>
    <?}?>
    </td>
    <td>
    <p align="center">
    <? if ($total['count(*)']>0) {?>
    вопросов: <? echo $total['count(*)']; ?> <a title="Список вопросов" id="listquestions<? echo $member['id']; ?>" href="javascript:;"><i class='fa fa-question-circle fa-lg'></i></a>
    <? if ($totaltd['count(*)']==0) {?>
    <a href="#" onClick="DelWindow(<? echo $member['id'];?> ,0,'delquestions&kid=<? echo $kid; ?>','questgroups','вопросы')" title="Удалить вопросы"><i class='fa fa-trash fa-lg'></i></a>
    <?}?>
    <?}?>
    </p>
    </td>
    <?
    mysqli_free_result($tottd);
    mysqli_free_result($totq);

    echo "<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    if (defined("IN_ADMIN")) {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$member['userid']."' LIMIT 1");
     $fromuser = mysqli_fetch_array($from);
     echo "<td align='center'><p><a href='edituser&id=".$member['userid']."'>".$fromuser['userfio']."</a></p></td>";
     mysqli_free_result($from);
    } 
    echo "</tr>";
  }
  mysqli_free_result($gst);
  echo "</table></div></p>";
 } 
  include "bottomadmin.php";
} else die;  
  
?>