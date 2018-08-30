<?php
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT")) 
  {
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  $ext = $_GET["ext"];
  if (empty($ext)) $ext = 0;

  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  $pa = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$selpaid."' LIMIT 1;");
  $paa = mysqli_fetch_array($pa);
  $paname = $paa['name'];
  $exlistname = $paa['exlistname'];
  if (!empty($exlistname))
   $exlistname = "(".$exlistname.")";
  $allowchange = false;
  if ((defined("IN_SUPERVISOR") and $paa['ownerid'] == USER_ID)) 
    $allowchange = true;


  if(defined("IN_ADMIN") or (defined("IN_SUPERVISOR") and $allowchange))
   $titlepage="Экспертиза проектов модели конкурса ".$paname." по всем участникам";
  else
   $titlepage="Экспертиза проектов модели конкурса ".$paname;
   
  if ($ext==1) {
   $titlepage.=" (Расширенная форма)";
  } 
  
  include "topadmin.php";
?>
<script>
   $(document).ready(function() {
			$('.fancybox').fancybox();
 	 });
   
  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    

  function showresult(signature,tid){
    $.fancybox.close();
		$.fancybox.open({
					href : 'testresults&tid='+tid+'&sign='+signature+'&url=lists|paid=<? echo $selpaid; ?>',
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
</script>
<style type="text/css">
.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
}
</style>
<?

  // Проверим на дату начала и окончания экспертизы
  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$ik);
  $day=$ik[1][0];
  $month=$ik[2][0];
  $year=$ik[3][0];
  $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
  $date1 = $paa['checkdate1'];
  $date2 = $paa['checkdate2'];
  $arr1 = explode(" ", $date1);
  $arr2 = explode(" ", $date2);  
  $arrdate1 = explode("-", $arr1[0]);
  $arrdate2 = explode("-", $arr2[0]);
  $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
  $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
  
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
  {
  }
  else
  {
  if ($timestamp3 < $timestamp1)
   {
    ?>
           <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Экспертиза проектов еще не началась!</p>
            	</div>
           </div>  
               <?
       die;
   } 
   else
   if ($timestamp3 > $timestamp2)
   {
    ?>
           <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Экспертиза проектов завершена!</p>
            	</div>
           </div>  
               <?
       die;
   }  
  }
  
  if(defined("IN_EXPERT") or defined("IN_SUPERVISOR"))
  {
      // Добавлено тестирование
      $goahead = 1;
      if ($paa['testblock']>0) 
      {
        // Проверим результат, если результат есть и порог пройден - допутим к созданию проекта
        $userid = USER_ID;
        // Найдем тест, который должен пройти участник
        $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE testfor='expert' AND enable='1' AND proarrid='".$selpaid."' ORDER BY id LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM singleresult WHERE userid='".$userid."' AND testid='".$testdata['id']."' ORDER BY id;");
        if (!$res) 
          puterror("Ошибка при обращении к базе данных");
        $maxuserball=0;
        $attempts=0;
        // Просканируем попытки участника
        while($resdata = mysqli_fetch_array($res))
         {
           $attempts++;
           $percent = (int) floor($resdata['rightball'] / $resdata['allball'] * 100);
           if ($percent > $maxuserball)
            $maxuserball = $percent;
         } 
        mysqli_free_result($res); 
        // Нашли максимальный балл
        if ($maxuserball < $testdata['maxball'])
          {
           // Участник не может проходить дальше - нужно пройти тест
           $goahead = 0;
           $testname = $testdata['name'];
           $testsign = $testdata['signature'];
           $testball = $testdata['maxball'];
           $testattempt = $testdata['attempt'];
           
    ?>
    
<script type="text/javascript">
		$(document).ready(function() {
    	$("#viewtest").click(function() {
				$.fancybox.open({
					href : 'viewtest&s=<? echo $testsign ?>',
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
    
    <?
           
          }
        mysql_free_result($test); 
      }


      if ($goahead==0 and $paa['testblock']>0)
        {
        // Если нет - предложим тест
        $zz++;
        $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE testfor='expert' AND enable='1' AND proarrid='".$selpaid."' LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        $cntatt = $testattempt - $attempts;
        echo "<div class='menu_glide_tops'>";
        echo "<table border='0'>";
        echo "<tr><td>";

        if ($testattempt>0 and $cntatt<=0)
        {
  ?>
           <div class="ui-widget">
	            <div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Попытки пройти тест "<? echo $testname; ?>" закончились. Экспертиза проектов модели конкурса <strong><? echo $paname; ?></strong> запрещена.</p>
            	</div>
            </div>
   <?
        }
        else
        {
        

        echo "<p align='center' style='font-size:1.1em;'>Перед экспертизой проектов модели конкурса <strong>".$paname."</strong> необходимо пройти тест:</p>";

      ?>
 <script>
  $(function() {
    $( "#viewtest<? echo $member['id']; ?>" ).button();
  });
</script>
      <?

        if ($testattempt==0)
         echo "<p align='center'><a style='font-size:1em;' id='viewtest' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a></p>";
        else
        {
         if ($cntatt>0)
           echo "<p align='center'><a style='font-size:1em;' id='viewtest' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a> Осталось попыток: ".$cntatt."</p>";
        }
        
        echo "<p align='center'><font face='Tahoma,Arial' size=-1>Для успешного прохождения теста и получения возможности экспертизы проектов, необходимо набрать не менее ".$testball."% баллов.</font></p>";
        
        }
        echo "</td></tr>"; 
        echo "</table></div>";
        mysql_free_result($test); 
      }
      else
      // Покажем кнопки проведения экспертизы
      if ($goahead==1)
      {

   $totmem = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized')");
   $total = mysqli_fetch_array($totmem);
   $allcount = $total['count(*)'];

   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".USER_ID."' AND s.exlistid='0'");
   $total = mysqli_fetch_array($tot);
   $count = $total['count(*)'];
   $memrealcnt = $allcount - $count;
  
   if ($memrealcnt>0) {
   ?>
  
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Для проведения экспертизы проектов необходимо нажать кнопку <strong>Новая экспертиза</strong>, выбрать один проект и нажать кнопку <strong>Продолжить</strong>. В случае проведения экспертизы в несколько туров (этапов), необходимо будет нажать кнопку <strong>Новая экспертиза</strong> с наименованием соответствующего этапа. Все проведенные Вами экспертизы фиксируются в системе. Помните, что экспертизу проекта можно провести только один раз. Также Вы можете всегда посмотреть все проведенные экспертизы.</p>
	</div>
</div>
   
   <script>
    $(function() {
     $( "#addlist" ).button();
    });
   </script>   
   <p align="center"><input type="button" style="font-size:120%;" name="addlist" id="addlist" value="<?echo "Новая экспертиза ".$exlistname; ?>" onclick="document.location='<? echo "addlist&paid=".$selpaid."&ext=".$ext; ?>'"></p>
   
   <?
//        echo"<p align='center'><a class=link href='addlist&paid=".$selpaid."&ext=".$ext."'><img src='img/b_newtbl.png'>&nbsp;Новая экспертиза ".$exlistname."</a></p>";
   }

   $ex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertcontentnames WHERE proarrid='".$selpaid."' ORDER BY id");
   if (!$ex) puterror("Ошибка при обращении к базе данных");
   $kk=0;
   while($exmember = mysqli_fetch_array($ex))
   {
    $kk++;
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".USER_ID."' AND s.exlistid='".$exmember['id']."'");
    $total = mysqli_fetch_array($tot);
    $count = $total['count(*)'];
    $memrealcnt = $allcount - $count;
    mysqli_free_result($tot);
    if ($memrealcnt>0) 
    ?>
  <script>
      $(function() {
       $( "#addlist<?echo $kk;?>" ).button();
      });
  </script>   
    <p align="center"><input type="button" style="font-size:120%;" name="addlist" id="addlist<?echo $kk;?>" value="<? echo "Новый экспертный лист (".$exmember['name'].")"; ?>" onclick="document.location='<? echo "addlist&paid=".$selpaid."&exlist=".$exmember['id']."&ext=".$ext; ?>'"></p>
    <?
  //   echo"<p align='center'><a class=link href='addlist&paid=".$selpaid."&exlist=".$exmember['id']."&ext=".$ext."'><img src='img/b_newtbl.png'>&nbsp;Новый экспертный лист (".$exmember['name'].")</a></p>";
   }
   }
  }
  
  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  if(defined("IN_ADMIN") or (defined("IN_SUPERVISOR") and $allowchange))
  {

 ?>
   <script>
    $(function() {
     $( "#expert" ).selectmenu({
      change: function( event, data ) {
        $('#getexpert').submit();
      }      
     });
    });
   </script>   
 <?

//    if (!empty($_GET["expert"]))
//     $_POST["expert"] = $_GET["expert"];

    $res1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM proexperts WHERE proarrid='".$selpaid."'");

    echo"<form id='getexpert' action='lists' method='get'>";
    echo"<input type='hidden' name='paid' value='".$selpaid."'>";
    echo"<input type='hidden' name='ext' value='".$ext."'>";
    echo"<p align='center'><select id='expert' name='expert'>";
    echo"<option value='0'>Все эксперты</option>";

    while($r1 = mysqli_fetch_array($res1))
    {
     $res2=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$r1['expertid']."'");
     $r2 = mysqli_fetch_array($res2);
     if (!empty($_GET["expert"]))
     {
      if ($r2[id]==$_GET["expert"]) 
       echo"<option value='".$r2[id]."' selected>".$r2['userfio']."</option>";
      else
       echo"<option value='".$r2[id]."'>".$r2['userfio']."</option>";
     }
     else 
      echo"<option value='".$r2[id]."'>".$r2['userfio']."</option>";
     mysqli_free_result($res2); 
    }
    echo"</select>";
    mysqli_free_result($res1); 

    if (!empty($_GET["expert"]))
     echo "&nbsp;<a target='_blank' href='print&paid=".$selpaid."&expert=".$_GET["expert"]."'><i class='fa fa-print fa-lg'></i> Печать экспертных листов</A></p>";
    echo "</p>";
    echo"</form>";

   // Запрашиваем общее число листов
   if (empty($_GET["expert"]) or $_GET["expert"]=='0') 
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."'");
   else 
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".$_GET["expert"]."'");
    
   if (empty($_GET["expert"]) or $_GET["expert"]=='0') 
    $lst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT s.* FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' ORDER BY s.ball DESC LIMIT $start, $pnumber;");
   else
    $lst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT s.* FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".$_GET["expert"]."' ORDER BY s.ball DESC LIMIT $start, $pnumber;");
  
   if (!$lst || !$tot) puterror("Ошибка при обращении к базе данных");

  } 
  else
  if (defined("IN_EXPERT") or defined("IN_SUPERVISOR"))
  {
   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".USER_ID."'");
   $lst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT s.* FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".USER_ID."' ORDER BY s.ball DESC LIMIT $start, $pnumber;");
   if (!$lst || !$tot) 
    puterror("Ошибка при обращении к базе данных");
  }
  
    ?>
    
<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
</script>

<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}
</style>

<script>
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
      active: false,
      collapsible: true
    });
  });
  </script>
  
  <div id="accordion">
<?

  // Найдем оценку проекта
  $res5=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT ocenka, exlistname FROM projectarray WHERE id='".$selpaid."'");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysqli_fetch_array($res5);
  $ocenka = $proarray['ocenka'];
  $exlistname = $proarray['exlistname'];
  mysqli_free_result($res5);
  if (!empty($exlistname))
   $exlistname = ' - '.$exlistname;
  else 
   $exlistname = ' - по умолчанию';

  $i=$start;
  while($list = mysqli_fetch_array($lst))
  {
   // Покажем проект только если он входит в выбранный шаблон
   $res1=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$list['memberid']."'");
   $r1 = mysqli_fetch_array($res1);
   $paid = $r1['proarrid'];
   if ($selpaid == $paid) {
    
   $i=$i+1;

   $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$r1['userid']."'");
   $r3 = mysqli_fetch_array($res3);

   $exlistid = $list['exlistid'];
   if ($exlistid>0) {
    $ex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM expertcontentnames WHERE id='".$exlistid."'");
    if (!$ex) puterror("Ошибка при обращении к базе данных");
    $exmember = mysqli_fetch_array($ex);
    $exname = " Экспертный лист №".$list['id']." (".$exmember['name'].")";
   } else
    $exname = " Экспертный лист №".$list['id']." ".$exlistname;

   echo "<h3><i class='fa fa-thumbs-up fa-lg'></i> ".$exname.", проект №".$r1['id']." ".$r1['info']." (автор ".$r3['userfio'].")</h3>";

   echo "<div>";
  
?> 
<script type="text/javascript">
		$(document).ready(function() {
    	$("#fancybox<?php echo $r1['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewproject3&id=<? echo $r1['id'] ?>',
					type : 'iframe',
          width : 1000,
					padding : 5
				});
			});

      $("#fancybox-manual-<?php echo $r1['id']; ?>").click(function() {
				$.fancybox.open([
        <?php
  $res33 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$r1['proarrid']."' ORDER BY id");
  while($param = mysqli_fetch_array($res33))
   { 
    $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$r1['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysqli_fetch_array($res4);
    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) { 
     if ($param['filetype']=="foto") {
      echo "{ href : '".$upload_dir.$param4['projectid'].$param4['realfilename']."' },";
     }
     }  
    }
    mysqli_free_result($res4); 
    }
    mysqli_free_result($res33);
        ?>
          
				], {
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});      
      
		});
</script>
<?

    if(defined("IN_ADMIN"))
    {
     if ($list['userid']>0) {
      $res2=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$list['userid']."' LIMIT 1");
      $r2 = mysqli_fetch_array($res2);
      echo "<p>Эксперт - ".$r2['userfio'];
      mysqli_free_result($res2);
     }
     else
      echo "<p>Эксперт - Инкогнито";
     
     echo "&nbsp;&nbsp;<a href='#' onClick='DelList(".$list['id'].",".$start.",".$i.");' title='Удалить лист'><i class='fa fa-trash fa-lg'></i></a></p>"; 
    }
    
    echo "</p>";

    if ($list['maxball']!=0) 
     $percent = ($list['ball'] / $list['maxball']) * $ocenka;  
    else
     $percent = 0;
    
    echo "<p>Набрано баллов ".$list['ball']." из ".$list['maxball']." (Средний балл ".round($percent,2)."). 
    Дата проведения экспертизы ".data_convert ($list['puttime'], 1, 1, 0)."</p>";
    
    if (empty($list['info'])) {
    }
    else 
     echo "<p><b>Экспертное заключение:</b> ".$list['info']."</p>";

// Покажем расширенную информацию
?>
<script type="text/javascript">
		$(document).ready(function() {
    	$("#fancyboxcontent<?php echo $list['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewlist3&exlistid=<? echo $exlistid; ?>&paid=<? echo $selpaid; ?>&listid=<? echo $list['id']; ?>&listball=<? echo $list['ball']; ?>',
					type : 'iframe',
          width : 600,
					padding : 5
				});
			});
		});
</script>
<hr>

<p><b><a title="Просмотр проекта" target="_blank" href="viewproject3&id=<? echo $r1['id'] ?>">
<i class='fa fa-search fa-lg'></i> Просмотр проекта <? echo $r1['info']; ?></a></b></p>

<p><b><a title="Проcмотр экспертного листа" id="fancyboxcontent<? echo $list['id'] ?>" href="javascript:;">
<i class='fa fa-search fa-lg'></i> Проcмотр экспертного листа</a></b></p>

<?
     echo "</div>";
    
   }
   
   mysqli_free_result($res1);
   mysqli_free_result($res3);
  }
    echo "</div>";

  // Выводим ссылки на предыдущие и следующие 
  $total = mysqli_fetch_array($tot);
  $count = $total['count(*)'];
  $count2 = $total['count(*)'];
  echo "<p>";
  $i=1;
  $start2 = 0;
  if ($count>$pnumber)
  while ($count > 0)
  {
    if ($start==$start2)
     echo $i."&nbsp;";
    else {
     if (!empty($_GET["expert"]))
     {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='lists&ext=<? echo $ext; ?>&paid=<? echo $selpaid; ?>&expert=<? echo $_GET["expert"]; ?>&start=<? echo $start2; ?>'">&nbsp;
     <?
     }
     else
     {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='lists&ext=<? echo $ext; ?>&paid=<? echo $selpaid; ?>&start=<? echo $start2; ?>'">&nbsp;
     <?
     }
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";
  if ($count2>0) echo"<p align='center'>Всего экспертных листов - ".$count2."</p>";
  include "bottomadmin.php";
  } 
  else die;
?>

