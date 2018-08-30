<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Список тестов";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];
  if (empty($paid))
   $paid=0;

  $gst3 = mysqli_query($mysqli,"SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $paid == 0) or (defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

    if ($paid!=0) maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);


?>
<? if ($paid==0) {?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Тесты, создаваемые в данном разделе, могут использоваться только для самостоятельного пробного тестирования.</p>
	</div>
</div>
<?} else {?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Тесты, созданные в модели проекта, могут использоваться как для входного тестирования участников перед размещением готовых проектов, так и для тестирования экспертов на предмет знаний в различных областях перед началом проведения экспертизы. Тестов может быть несколько, но активными для участников и экспертов может быть только по одному тесту.</p>
	</div>
</div>
<?}?>
<p></p>

  <?
  if ($paid==0)
   $proba = "пробный ";
  else
   $proba = "";
  if (defined("IN_ADMIN")) {
?>
<script type="text/javascript">
  $(function() {
    $("#addtest").button();
  });  
	$(document).ready(function() {
			$('.fancybox').fancybox();
    	$("#addtest").click(function() {
				$.fancybox.open({
					href : 'addtest&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 600,
          height : 400,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
	});
 </script> 
<?
    echo "<p align='center'><a style='font-size:1em;' id='addtest' href='javascript:;'><i class='fa fa-question-circle fa-lg'></i>&nbsp;Добавить новый ".$proba."тест</a></p><p align='center'>";
  }
  else
  if (defined("IN_SUPERVISOR"))
  {
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM testgroups WHERE ownerid='".USER_ID."' ORDER BY id");
    $totalt = mysqli_fetch_array($tot);
    if (LOWSUPERVISOR)
    {
     if ($totalt['count(*)']==0) {
?>
<script type="text/javascript">
  $(function() {
    $("#addtest").button();
  });  
	$(document).ready(function() {
			$('.fancybox').fancybox();
    	$("#addtest").click(function() {
				$.fancybox.open({
					href : 'addtest&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 600,
          height : 400,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
	});
 </script> 
<?
      echo "<p align='center'><a style='font-size:1em;' id='addtest' href='javascript:;'><i class='fa fa-question-circle fa-lg'></i>&nbsp;Добавить новый ".$proba."тест</a></p><p align='center'>";
     }
    }
    else
    {
?>
<script type="text/javascript">
  $(function() {
    $("#addtest").button();
  });  
	$(document).ready(function() {
			$('.fancybox').fancybox();
    	$("#addtest").click(function() {
				$.fancybox.open({
					href : 'addtest&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 600,
          height : 400,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
	});
 </script> 
<?
      echo "<p align='center'><a style='font-size:1em;' id='addtest' href='javascript:;'><i class='fa fa-question-circle fa-lg'></i>&nbsp;Добавить новый ".$proba."тест</a></p><p align='center'>";
    }
    mysqli_free_result($tot);
  }

  if (defined("IN_ADMIN")) {
    if ($paid==0)
     $gst = mysqli_query($mysqli,"SELECT * FROM testgroups ORDER BY id DESC;");
    else
     $gst = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE proarrid='".$paid."' ORDER BY id DESC;");
   }
  else
   {
    $gst = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE proarrid='".$paid."' AND ownerid='".USER_ID."' ORDER BY id DESC;");
   }
  if (!$gst) puterror("Ошибка при обращении к базе данных 1");

  if (defined("IN_ADMIN")) {
    if ($paid==0)
     $tot = mysqli_query($mysqli,"SELECT count(*) FROM testgroups;");
    else
     $tot = mysqli_query($mysqli,"SELECT count(*) FROM testgroups WHERE proarrid='".$paid."';");
   }
  else
   {
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM testgroups WHERE proarrid='".$paid."' AND ownerid='".USER_ID."' ORDER BY id;");
   }
  if (!$tot) puterror("Ошибка при обращении к базе данных 1");
  $totc = mysqli_fetch_array($tot);
  $ctotal = $totc['count(*)'];
  mysqli_free_result($tot);
  if ($ctotal>0)
  {
  $tableheader = "class=tableheaderhide";

    ?>
<script type="text/javascript">
  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
  function showresult(signature,tid){
    $.fancybox.close();
		$.fancybox.open({
					href : 'testresults&tid='+tid+'&sign='+signature,
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          showCloseButton : false,
          modal : true,
					padding : 5
				});
   }
  function closeFancybox(){
    $.fancybox.close();
   }
  function Resume() {
    $('#defaultCountdown').countdown('resume');
  } 
</script>    

 <div id='menu_glide' class='menu_glide'>
      <table width="100%" style="background-color: #FFFFFF;" align='center' class=bodytable border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='25'><p class=help>№</p></td>
              <td witdh='25'><p class=help></p></td>
              <td witdh='500'><p class=help>Наименование теста</p></td>
              <td witdh='120'><p class=help>Итоговый балл</p></td>
              <td witdh='100'><p class=help>Время тестирования</p></td>
              <td witdh='400'><p class=help>Группы вопросов</p></td>
              <td witdh='100'><p class=help>Дата создания</p></td>
              <? if (defined("IN_ADMIN")) {?><td align='center' witdh='100'><p class=help>Автор теста или модель</p></td><?}?>
              <td witdh='25'><i style="color:#fff;" class='fa fa-play-circle fa-2x' title='Просмотр теста'></i></td>
              <td witdh='25'><i style="color:#fff;" class='fa fa-bar-chart fa-2x' title='Результаты тестирования'></i></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysqli_fetch_array($gst))
  {
       $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult WHERE testid='".$member['id']."' LIMIT 1;");
       if (!$counttest) puterror("Ошибка при обращении к базе данных");
       $cnttests = mysqli_fetch_array($counttest);
       $count_res = $cnttests['count(*)'];
       mysqli_free_result($counttest); 

?>

<script type="text/javascript">
		$(document).ready(function() {
    	$("#edittest<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'edittest&paid=<? echo $paid ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 600,
          height : 500,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    	$("#addgrouptotest<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'addgrouptotest&paid=<? echo $paid ?>&tid=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 1000,
          height : 500,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      });
</script>

    <?  
    if ($member['enable'])
     echo "<tr style='background: #D1D4D8;'><td witdh='25' align='center'><p>".++$i."</p></td>";
    else
     echo "<tr><td witdh='25' align='center'><p>".++$i."</p></td>";
    ?>
    <td width='25' align='center'><p>
    <? if ($member['testfor']=='expert') { ?><i class='fa fa-user-md fa-2x' title='Тест для экспертов'></i><? } else 
    if ($member['testfor']=='member') {?>
    <i class='fa fa-user fa-2x' title='Тест для участников'></i><?}
    else {?>
    <i class='fa fa-slideshare fa-2x' title='Пробный тест'></i><? 
    }?>
    </p></td>
    <? 
    echo "<td width='500'>";
    echo "<p>";
    echo $member['name'];
    echo "&nbsp;<a title='Редактировать параметры теста' id='edittest".$member['id']."' href='javascript:;'><i class='fa fa-cog fa-lg'></i></a>";
    if ($paid==0)
     echo "&nbsp;<a title='Расшарить пробный тест' id='sharetest".$member['id']."' href='javascript:;'><i class='fa fa-share-alt fa-lg'></i></a>";

      $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='".$member['id']."' ORDER BY id");
      if (!$td) puterror("Ошибка при обращении к базе данных");
      $qc=0;
      $tt=0;
      $sumball=0;
      $b="";
      $grq = 0;
      while($testdata = mysqli_fetch_array($td))
      {
       $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name, singleball, singletime FROM questgroups WHERE id='".$testdata['groupid']."' LIMIT 1");
       $questgroup = mysqli_fetch_array($qg);
       $b .= "<script>		\$(document).ready(function() {
      	\$(\"#editgroupintest".$testdata['id']."\").click(function() {
				\$.fancybox.open({
					href : 'editgroupintest&paid=".$paid."&id=".$testdata['id']."',
					type : 'iframe',
          width : 1000,
          height : 400,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
		  	});";
       $b .= "});</script>"; 
       if ($testdata['random']) 
        $c = "<b>".$questgroup['name']."</b>"; 
       else
        $c = $questgroup['name'];

       if ($count_res>0)
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> ".$c."
        - ".$testdata['qcount']." вопрос(ов)</p>";
       else
       if ($member['enable'])
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> ".$c."
        - ".$testdata['qcount']." вопрос(ов)</p>";
       else
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> <a title='Редактировать параметры группы в тесте' id='editgroupintest".$testdata['id']."' href='javascript:;'>".$c."</a>
        - ".$testdata['qcount']." вопрос(ов) <a href='#' onClick=\"DelWindowPaid(".$testdata['id']." ,".$paid.",'deltestdata','testoptions','группу в тесте')\" title='Удалить вопросы из теста'>
        <i class='fa fa-trash fa-lg'></i></a></p>";
       
       $qc += $testdata['qcount'];
       $tt += $questgroup['singletime']*$testdata['qcount'];
       $sumball += $questgroup['singleball']*$testdata['qcount'];
       $grq++;
      }
      mysqli_free_result($td); 

      if ($count_res>0)
       $b = "<script> \$(function() {\$( \"#accordion".$member['id']."\" ).accordion({heightStyle: \"content\",collapsible: true, active: false
       });});</script><div id='accordion".$member['id']."'><h3 style='font-size:12px; color: #fff;'><b>Групп: ".$grq.", всего вопросов - ".$qc."</b></h3><div>".$b."
       </div></div>";
      else
      if ($member['enable'])
       $b = "<script> \$(function() {\$( \"#accordion".$member['id']."\" ).accordion({heightStyle: \"content\",collapsible: true, active: false
       });});</script><div id='accordion".$member['id']."'><h3 style='font-size:12px; color: #fff;'><b>Групп: ".$grq.", всего вопросов - ".$qc."</b></h3><div>".$b."
       </div></div>";
      else
       $b = "<script> \$(function() {\$( \"#accordion".$member['id']."\" ).accordion({heightStyle: \"content\",collapsible: true, active: false
       });});</script><div id='accordion".$member['id']."'><h3 style='font-size:12px; color: #fff;'><b>Групп: ".$grq.", всего вопросов - ".$qc."</b></h3><div>".$b."
       <p><a id='addgrouptotest".$member['id']."' href='javascript:;'><i class='fa fa-plus-square fa-lg' title='Добавить еще вопросы в тест'></i></a></p></div></div>";

      
      if ($qc==0) {
       $b = "<p><b>Пока нет вопросов.</b> <a id='addgrouptotest".$member['id']."' href='javascript:;'><i class='fa fa-plus-square fa-lg' title='Добавить вопросы в тест'></i></a></p>";
    ?>
    &nbsp;<a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'deltest','testoptions','тест')" title="Удалить тест"><i class='fa fa-trash fa-lg'></i></a>
    <?}?>
    </p></td>
    <td width='120' align='center'><p><? 
    echo $sumball." (порог ".$member['maxball']."%)";  
    ?></p></td>
    <td width='100' align='center'><p>
    <? 
     if ($tt>=60) 
     {
       echo (int) floor($tt / 60)." ч. ".($tt % 60)." мин.";
       $hours = (int) floor($tt / 60);
       $minutes = $tt % 60;
       
     } 
     else
     {
       echo $tt." мин.";
       $hours = 0;
       $minutes = $tt;
     }
    ?>
    </p></td>
    
<script type="text/javascript">
		$(document).ready(function() {
    	$("#viewtest<?php echo $i; ?>").click(function() {
				$.fancybox.open({
					href : 'viewtest&s=<? echo $member['signature'] ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
      });
</script>
    
    <td width='400'><? echo $b; ?></td>
    <?
    echo "<td><p class=zag2>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";

    if (defined("IN_ADMIN")) {
     if ($paid==0)
     {
      if ($member['proarrid']==0) {
       $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$member['ownerid']."' LIMIT 1;");
       $fromuser = mysqli_fetch_array($from);
       echo "<td><p class=zag2><a href='edituser&id=".$member['ownerid']."'>".$fromuser['userfio']."</a></p></td>";
       mysqli_free_result($from);
      }
      else
      {
       $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM projectarray WHERE id='".$member['proarrid']."' LIMIT 1;");
       $frompa = mysqli_fetch_array($from);
       echo "<td><p class=zag2>".$frompa['name']."</p></td>";
       mysqli_free_result($from);
      }
     }
     else
     {
      $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM projectarray WHERE id='".$member['proarrid']."' LIMIT 1;");
      $frompa = mysqli_fetch_array($from);
      echo "<td><p class=zag2>".$frompa['name']."</p></td>";
      mysqli_free_result($from);
     }
    } 

    if ($qc>0)
        echo "<td><a title='Просмотр теста' id='viewtest".$i."' href='javascript:;'><i class='fa fa-play-circle fa-lg'></i></a></td>";
    else
        echo "<td></td>";
    
    if ($count_res>0)
        echo "<td><a title='Результаты - ".$count_res."' href='viewtestresults&tid=".$member['id']."'><i class='fa fa-bar-chart fa-lg'></i></a></td>";
    else
        echo "<td></td>";
        
    echo "</tr>";
  }
  
  echo "</table></div></p>";
 }     
  include "bottomadmin.php";
}} else die;  
  
?>