<?php
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $arc = $_GET["arc"];
  if (empty($arc)) $arc = 0;

  if ($arc==0) 
   $title=$titlepage="Модели проектов";
   else
   $title=$titlepage="Архивные модели проектов";
   
  // Выводим шапку страницы
  include "topadmin.php";

//  if ($arc==0) {
  
/*  if(defined("IN_ADMIN")) 
  {
   echo"<p align='center'>
   <a href='javascript:;' id='addproarr' style='font-size:1em;'><i class='fa fa-cubes fa-lg'></i> Добавить новую модель</a></p>";
  }
  else
  if (defined("IN_SUPERVISOR"))
  {
   $tot2 = mysql_query("SELECT count(*) FROM projectarray WHERE ownerid='".USER_ID."'");
   if (!$tot2) puterror("Ошибка при обращении к базе данных");
   $total2 = mysql_fetch_array($tot2);
   $tot3 = mysql_query("SELECT pacount FROM users WHERE id='".USER_ID."'");
   if (!$tot3) puterror("Ошибка при обращении к базе данных");
   $total3 = mysql_fetch_array($tot3);
   $count = $total2['count(*)'];
   if ($count < $total3['pacount']) 
   {
    echo"<p align='center'>
    <a href='javascript:;' id='addproarr' style='font-size:1em;'><i class='fa fa-cubes fa-lg'></i> Добавить новую модель</a></p>";
   }
  } */
//  }

  echo"<p align='center'>";
  

  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  if ($arc==0) {
   
   if(defined("IN_ADMIN")) 
   {
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projectarray WHERE closed='0'");
    $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE closed='0' ORDER BY id DESC LIMIT $start, $pnumber");
   }
   else
   {
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projectarray WHERE ownerid='".USER_ID."' AND closed='0'");
    $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE ownerid='".USER_ID."' AND closed='0' ORDER BY id DESC LIMIT $start, $pnumber");
   }

  }
  else
  {
   if(defined("IN_ADMIN")) 
   {
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projectarray WHERE closed='1'");
    $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE closed='1' ORDER BY id DESC LIMIT $start, $pnumber");
   }
   else
   {
    $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projectarray WHERE ownerid='".USER_ID."' AND closed='1'");
    $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE ownerid='".USER_ID."' AND closed='1' ORDER BY id DESC LIMIT $start, $pnumber");
   }
  
  }
  if (!$gst|| !$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
  $total = mysqli_fetch_array($tot);
  $countz = $total['count(*)'];
  if ($countz==0)
  {
  ?>
            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Нет созданных моделей.</p>
            	</div>
            </div><p></p>
   <?
  } else
  {

  ?>
  <script>
   $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
      collapsible: true
    });
  });
  </script>
  
  <div id="accordion">
  <?         

  $i=$start;
  while($member = mysqli_fetch_array($gst))
  {
    ++$i;
    
   // $knowfrom = mysql_query("SELECT * FROM knowledge WHERE id='".$member['knowledge_id']."'");
   // $know = mysql_fetch_array($knowfrom);

    echo "<h3 style='font-size:14px;'>";

//    if ($member['closed']==0)
//     echo "<strong> ".$member['name']." [".data_convert ($member['startdate'], 1, 0, 0)." | ".data_convert ($member['checkdate1'], 1, 0, 0)." | ".data_convert ($member['checkdate2'], 1, 0, 0)." | ".data_convert ($member['stopdate'], 1, 0, 0)."]</strong>";
//    else
     echo " ".$member['name']."<div style='color;#ccc;'>".data_convert ($member['startdate'], 1, 0, 0)." | ".data_convert ($member['checkdate1'], 1, 0, 0)." | ".data_convert ($member['checkdate2'], 1, 0, 0)." | ".data_convert ($member['stopdate'], 1, 0, 0)."</div>";
    echo "</h3>";

    echo"<div>";

    maintab($mysqli, $member['id'], $member['name'], $member['testblock'], $member['payment'], false);

    echo "<p>".$member['comment']."</p>";

    if(defined("IN_ADMIN")) {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['ownerid']."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     echo "<p>Автор модели: ";
     echo "<a class='menu' href='edituser&id=".$member['ownerid']."' title='Данные создателя'>".$fromuser['userfio']."</a></p>";
    }
    
    if ($member['payment']>0) 
     echo "<p>Сумма оплаты за размещение проекта: <b>".$member['paysumma']."</b> руб.</p>";
     
//    echo "<p>Ключ активации эксперта: <b>".$member['expertkey']."</b></p>";

    echo "<p>Отчеты: <i class='fa fa-area-chart fa-lg'></i> <a href='report2&mode=0&paid=".$member['id']."'>Итоговый рейтинг проектов</a>";
    if (!LOWSUPERVISOR)
    {
     if ($member['openexpert']==0) 
      echo " | <i class='fa fa-area-chart fa-lg'></i> <a href='report2&mode=1&&paid=".$member['id']."'>Расширенный рейтинг по экспертам</a>";
     echo " | <i class='fa fa-area-chart fa-lg'></i> <a href='report2&mode=2&paid=".$member['id']."'>Расширенный рейтинг по критериям</a>";
     if ($member['openexpert']==0) 
      echo " | <i class='fa fa-bar-chart-o fa-lg'></i> <a href='stat&paid=".$member['id']."'>Статистика по экспертам</a></li>";
     echo " | <i class='fa fa-area-chart fa-lg'></i> <a href='report3&mode=0&paid=".$member['id']."'>Анализ критериев оценки проекта</a></p>";
    }
    if ($member['openexpert']>0) 
    {
// Проверим на дату начала и окончания экспертизы
  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$ik);
  $day=$ik[1][0];
  $month=$ik[2][0];
  $year=$ik[3][0];
  $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
  $date1 = $member['checkdate1'];
  $date2 = $member['checkdate2'];
  $arr1 = explode(" ", $date1);
  $arr2 = explode(" ", $date2);  
  $arrdate1 = explode("-", $arr1[0]);
  $arrdate2 = explode("-", $arr2[0]);
  $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
  $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
  if ($timestamp3 >= $timestamp1 and $timestamp3 <= $timestamp2)
    {

     echo "<div id='menu_glide' class='menu_glide_shadow'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td>";
     echo "<p align='center'><b><a href='addlist&paid=".$member['id']."&ext=0&sl=".$member['openexperturl']."'>Ссылка на проведение открытой экспертизы (лист по умолчанию)</a></b></p></td></tr>";
     
     $ex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertcontentnames WHERE proarrid='".$member['id']."' ORDER BY id");
     if (!$ex) puterror("Ошибка при обращении к базе данных");
     while($exmember = mysqli_fetch_array($ex))
       echo "<tr><td><p align='center'><b><a href='addlist&paid=".$member['id']."&ext=0&exlist=".$exmember['id']."&sl=".$member['openexperturl']."'>Ссылка на проведение открытой экспертизы (".$exmember['name'].")</a></b></p></td></tr>";

     echo "</table></div>";
    }
   }
    echo "</div>"; 
  }
  
  echo "</div>";

  // Выводим ссылки на предыдущие и следующие 
  if ($start > 0) echo " <p><a href='parray&arc=".$arc."&start=".($start - $pnumber)."'>Предыдущие</a></p";
  if ($countz > $start + $pnumber)  echo " <p><a href='parray&arc=".$arc."&start=".($start + $pnumber)."'>Следующие</a></p";
}
  include "bottomadmin.php";
} else die;  



?>