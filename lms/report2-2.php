<?php
if(defined("IN_ADMIN")) 
{

  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  $paname = $_GET["paname"];
  $mode = $_GET["mode"];
  $w = $_GET["w"];

  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  // Найдем оценку проекта
  $res5=mysql_query("SELECT openproject, ocenka FROM projectarray WHERE id='".$selpaid."'");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysql_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  
  // Посмотрим открытый ли проект
  if ($openproject==1 || defined("IN_ADMIN") || defined("IN_SUPERVISOR")) {

  $title=$titlepage="Итоговый рейтинг &#8220;".$paname."&#8221; по всем участникам";

  include "topadmin.php";

  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  // Запрашиваем общее число участников
  $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published')");
  $lst = mysql_query("SELECT * FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published') ORDER BY maxball DESC LIMIT $start, $pnumber;");
  
  if (!$lst || !$tot) puterror("Ошибка при обращении к базе данных");
  // При помощи цикла выбираем из базы данных
  // сообщения
  $n=$start;
  ?>
     
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable style="table-layout:fixed" width="750" border="0" cellpadding=5 cellspacing=5 bordercolorlight=gray bordercolordark=white align=center>
          <tr class=tableheader>
              <td width="50" align='center'><p>Место в рейтинге</p></td>
              <td width="300" style="overflow:hidden;" align='center'><p>Наименование проекта</p></td>
  <? if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
  { ?>
              <td width="100" style="overflow:hidden;" align='center'><p>Фамилия Имя Отчество участника</p></td>
              <td width="100" style="overflow:hidden;" align='center'><p>Место работы</p></td>
  <? }           
   if ($mode==1) 
   {
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {
    $lst2 = mysql_query("SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
    if (!$lst2) puterror("Ошибка при обращении к базе данных");
    while($list2 = mysql_fetch_array($lst2)) {
     $res2=mysql_query("SELECT * FROM users WHERE id='".$list2['expertid']."'");
     $r2 = mysql_fetch_array($res2);
     echo "<td width='150' align='center'><p>Эксперт ".$r2['userfio']."</p></td>";
    }
    }
   }
   
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Сумма средних баллов (рейтинг)</p></td>";
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Средний балл по рейтингу</p></td>";
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Проведено экспертиз</p></td></tr>";


/*    if ($w==1)
    {
     mysql_query("LOCK TABLES projects WRITE");
     mysql_query("SET AUTOCOMMIT = 0");
    } */
    
  while($list = mysql_fetch_array($lst))
  {
    $n=$n+1;
    if ($n<11)
     echo "<tr bgcolor='#FFFFFF'>";
    else
     echo "<tr>";
    echo "<td align='center'><p class=zag2>".$n."</p></td>";
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
//     echo "<td width='400'><p class=zag2>№".$list['id']." (".$list['info'].")</p></td>";
     echo "<td width='200' style='overflow:hidden;'><p class=zag2><a href='editproject&id=".$list['id']."' title='Изменить проект'>№".$list['id'].". ".$list['info']."</a></p></td>";
    else 
    {
     $res3cnt=mysql_query("SELECT count(*) FROM shablondb WHERE memberid='".$list['id']."' AND LENGTH(info)>0");
     $param3cnt = mysql_fetch_array($res3cnt);
     
     if ($param3cnt['count(*)']>0)
     {

?> 
<script type="text/javascript">
		$(document).ready(function() {
    	$("#fancybox<?php echo $list['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewcomment3&id=<? echo $list['id'] ?>',
					type : 'iframe',
					padding : 5
				});
			});
		});
</script>
<?
      
      $commstr = "<font size='-2'>
      <a title='Комментарии экспертов' id='fancybox".$list['id']."' href='javascript:;'>Комментарии экспертов (".$param3cnt['count(*)'].")</a>
      </font>";
     } 
     else 
      $commstr = "";
      
     if (isUrl($list['info']))
     {
      if (preg_match("/http:/i", $list['info'])>0)
       echo "<td width='300' style='overflow:hidden;'><p class=zag2><a href='".$list['info']."' target='_blank'>Проект №".$list['id'].". ".$list['info']."</a></p>".$commstr."</td>";
      else
       echo "<td width='300' style='overflow:hidden;'><p class=zag2><a href='http://".$list['info']."' target='_blank'>Проект №".$list['id'].". ".$list['info']."</a></p>".$commstr."</td>";
     }
 
     else
     {
      echo "<td width='300' style='overflow:hidden;'><p class=zag2>Проект №".$list['id'].". ".$list['info']."</p>".$commstr."</td>";
     }
    }
    
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
    {
     $res2=mysql_query("SELECT * FROM users WHERE id='".$list['userid']."'");
     $r2 = mysql_fetch_array($res2);
//     echo "<td width='200'><p class=zag2>".$r2['email']."</p></td>";
     echo "<td width='100' style='overflow:hidden;'><p class=zag2>".$r2['userfio']."</p></td>";
     echo "<td width='100' style='overflow:hidden;'><p class=zag2>".$r2['job']."</p></td>";
    }
    
    $lst3 = mysql_query("SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
    if (!$lst3) puterror("Ошибка при обращении к базе данных");
    $i=0;
    $newprcent = 0;
    
    //while($list3 = mysql_fetch_array($lst3))
    // {
      $subit = "";

      $lst4 = mysql_query("SELECT ball,maxball FROM shablondb WHERE memberid='".$list['id']."' AND exlistid='0'");
      if (!$lst4) puterror("Ошибка при обращении к базе данных");
    //  $list4 = mysql_fetch_array($lst4);
      
  
      while($list4 = mysql_fetch_array($lst4))
      {
//      $cntlst4 = mysql_query("SELECT COUNT(ball) FROM shablondb WHERE memberid='".$list['id']."' AND exlistid='0'");
//      if (!$cntlst4) puterror("Ошибка при обращении к базе данных");
//      $cntlist4 = mysql_fetch_array($cntlst4);
      
      if ($list4['maxball']!=0) 
      {
       $percent = ($list4['ball'] / $list4['maxball']) * $ocenka;  
//       $i += $cntlist4['COUNT(ball)'];
       $i++;
      }
      else
       $percent = 0;
//      echo $percent + " ";
      $newprcent += $percent; 
      $yessubit = false;
      if (($percent>0) || ($list4['maxball']!=0)) {
        if (!empty($exlistname))
         $subit .= $exlistname." - ";
//        $subit .= $list4['SUM(ball)']." из ".$list4['SUM(maxball)']." (".round($percent,2).")";
        $yessubit = true;
      }
       
    /*  $ex = mysql_query("SELECT * FROM expertcontentnames WHERE proarrid='".$selpaid."' ORDER BY id");
      if (!$ex) puterror("Ошибка при обращении к базе данных");
      while($exmember = mysql_fetch_array($ex))
      {  
     
     // $lst4 = mysql_query("SELECT SUM(ball),SUM(maxball) FROM shablondb WHERE memberid='".$list['id']."' AND exlistid='".$exmember['id']."'");
     // if (!$lst4) puterror("Ошибка при обращении к базе данных");
     // $list4 = mysql_fetch_array($lst4);
      
      $cntlst4 = mysql_query("SELECT COUNT(ball) FROM shablondb WHERE memberid='".$list['id']."' AND exlistid='".$exmember['id']."'");
      if (!$cntlst4) puterror("Ошибка при обращении к базе данных");
      $cntlist4 = mysql_fetch_array($cntlst4);
      
      if ($list4['SUM(maxball)']!=0) 
      {
       $percent = ($list4['SUM(ball)'] / $list4['SUM(maxball)']) * $ocenka;  
       $i += $cntlist4['COUNT(ball)'];
      }
      else
       $percent = 0;
      $newprcent = $newprcent + $percent; 
      if (($percent>0) || ($list4['SUM(maxball)']!=0)) {
        if ($yessubit)
         $subit .= ", ";
        $subit .= $exmember['name']." - ".$list4['SUM(ball)']." из ".$list4['SUM(maxball)']." (".round($percent,2).")";
       }
      } */
   
   if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
   {
   if ($mode==1) 
   {

     echo "<td align='center' width='150'><p>".$subit."</p></td>";

    }  
    }
     }
    
    if ($newprcent>0)
     echo "<td align='center' width='100'><p class=zag2>".round($newprcent,2)."</p></td>";
    else
     echo "<td align='center' width='100'><p class=zag2>-</p></td>";

    if ($w==1)
    {
      $q2 = mysql_query("UPDATE projects SET maxball=".$newprcent." WHERE id=".$list["id"]);
      if (!$q2) puterror("Ошибка при обращении к базе данных");
    }

    if ($i>0) 
    {
//     if ($list['maxball']>0)
//      $aball = $list['maxball'] / $i;
//     else
     if ($newprcent>0)
      $aball = $newprcent / $i;
     else 
      $aball = 0;
    }
    else
     $aball = 0;

    if ($aball>0)
     echo "<td align='center' width='100'><p class=zag2>".round($aball,2)."</p></td>";
    else  
     echo "<td align='center' width='100'>-</td>";

    if ($aball>0)
     echo "<td align='center' width='100'><p class=zag2>".$i."</p></td>";
    else  
     echo "<td align='center' width='100'>-</td>";
    
    echo "</tr>";

  }
    echo "</table></div>";
    if ($w==1)
    {
     mysql_query("COMMIT");
     mysql_query("UNLOCK TABLES");
    }
  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  echo "<p align='center'>";
  $i=1;
  $start2 = 0;
  if ($count>$pnumber)
  while ($count > 0)
  {
    if ($start==$start2)
     echo $i."&nbsp;";
    else {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='report2&mode=<? echo $mode; ?>&paname=<? echo $paname; ?>&paid=<? echo $selpaid; ?>&start=<? echo $start2; ?>'">&nbsp;
     <?
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";
  echo "<p><b>Пояснения к таблице итогового рейтинга.</b></p>";
  echo "<p>Итоговый рейтинг формируется в режиме реального времени в процессе оценки проектов экспертами. По мере того, как эксперты 
  заполняют экспертные листы, рейтинг автоматически пересчитывается. На оценку всех проектов отводится определенный срок. 
  После экспертизы всех проектов сформируется окончательный рейтинг.</p>";
  echo "<p>Итоговый рейтинг формируется по показателю <b>Сумма средних баллов</b>, который вычисляется на основании количества проведенных 
  экспертиз по данному проекту, полученному среднему баллу (обычно по стобалльной системе) по каждой экспертизе и суммарному итогу всех средних баллов.</p>";
  echo "<p>Показатель <b>Средний балл по рейтингу</b> вычисляется как отношение суммы средних баллов к количеству проведенных экспертиз.</p>"; 
  include "social.php";
  include "bottomadmin.php";
 } 
} else die; 
?>

