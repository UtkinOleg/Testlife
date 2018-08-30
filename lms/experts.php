<?php
if(defined("IN_ADMIN")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $super = $_GET["s"];
  if (empty($super))
  $super=0;

  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;
  
  if ($super==1)
   $title=$titlepage="Список супервизоров";
  else
   $title=$titlepage="Список экспертов";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";


  echo"<p align='center'><a class=link href='addexpert'><img src='img/b_newtbl.png'>&nbsp;Добавить нового эксперта</a>";
  echo"&nbsp;<a class=link href='msgs&to=expert'><img src='img/forum-default.png'>&nbsp;Отправить сообщение всем
  </a></p><p align='center'>";

  if ($super==1)
  {
   $tot = mysql_query("SELECT count(*) FROM users WHERE usertype='supervisor'");
   $gst = mysql_query("SELECT * FROM users WHERE usertype='supervisor' ORDER BY id DESC LIMIT $start, $pnumber;;");
  }
  else
  {
   $tot = mysql_query("SELECT count(*) FROM users WHERE usertype='expert'");
   $gst = mysql_query("SELECT * FROM users WHERE usertype='expert' ORDER BY id DESC LIMIT $start, $pnumber;;");
  }
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td witdh='50'></td>
              <td witdh='300'><p class=help>Эксперт (модератор)</p></td>
              <td witdh='200'><p class=help>Почта</p></td>
              <td witdh='200'><p class=help>Место работы</p></td>
              <? if ($super==1){?>
               <td><p class=help>Всего моделей</p></td>
               <td><p class=help>Количество созданных моделей</p></td>
               <td><p class=help>Ограниченный</p></td>
              <?}?>
              <td><p class=help>Экспертиза</p></td>
          </tr>   
     <?         

  $i = $start;
  while($member = mysql_fetch_array($gst))
  {
    echo "<tr><td witdh='50'><p>".++$i."</p></td>";
    echo "<td width='50'>";
    
        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$member['id'].$member['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='40'><div>"; 
        }  
    echo "</td><td width='300'><p>";
    echo "<a href='edituser&utype=expert&id=".$member['id']."' title='Редактировать эксперта'><p class=zag2>".$member['userfio']."</a>";
    echo "&nbsp;<a href='#' onClick='DelExpertWindow(".$member['id'].",0);' title='Удалить'><img src='img/b_drop.png' width='16' height='16'></a>";
    echo "&nbsp;<a href='msg&id=".$member['id']."' title='Отправить сообщение'><img src='img/forum-default.png' width='16' height='16'></a>";
    echo "</p></td>"; 
    echo "<td width='200'><p>".$member['email']."</p></td>";
    echo "<td width='200'><p>".$member['job']."</p></td>";

    if ($super==1)
    {
      echo "<td align='center'><p>".$member['pacount']."</p></td>";
      $gsto = mysql_query("SELECT count(ownerid) FROM projectarray WHERE ownerid='".$member['id']."'");
      if (!$gsto) puterror("Ошибка при обращении к базе данных");
      $pao = mysql_fetch_array($gsto);
      $ocount = $pao['count(ownerid)'];
      mysql_free_result($gsto);
      echo "<td align='center'><p>".$ocount."</p></td>";
      echo "<td align='center'><p>".$member['qcount']."</p></td>";
    }
    
    echo "<td>";
    
    $parr = mysql_query("SELECT * FROM proexperts WHERE expertid='".$member['id']."' ORDER BY id");
    $totecount=0;
    $a="";
    while($paex = mysql_fetch_array($parr))
    {
     $gst3 = mysql_query("SELECT name FROM projectarray WHERE id='".$paex['proarrid']."' ORDER BY name LIMIT 1");
     $parr3 = mysql_fetch_array($gst3);
     
     $etot = mysql_query("SELECT count(s.ball) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paex['proarrid']."' AND s.userid='".$member['id']."'");
     //$etot = mysql_query("SELECT count(*) FROM shablondb WHERE userid='".$member['id']."'");
     $etotal = mysql_fetch_array($etot);
     $ecount = $etotal['count(s.ball)'];
     
     $a.= "<p>".$parr3['name']." (".$ecount.")</p>";
     $totecount+=$ecount;
    }
    
    echo "<script> \$(function() {\$( \"#accordion".$member['id']."\" ).accordion({heightStyle: \"content\",collapsible: true, active: false
      });});</script><div id='accordion".$member['id']."'><h3 style='font-size:12px; color: #fff;'><b> Всего экспертиз: ".$totecount."</b></h3><div>".$a."
      </div></div>";

    echo "</td>";
    echo "</tr>";
  }
    echo "</table></div></p>";
  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];

  $cc = $count;
  echo "<p align=center>";
  $i=1;
  $start2 = 0;
  if ($count>$pnumber)
  while ($count > 0)
  {
    if ($start==$start2)
     echo $i."&nbsp;";
    else {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='experts&s=<? echo $super; ?>&start=<? echo $start2; ?>'">&nbsp;
     <?
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";

  if ($super==1)
   echo"<p align='center'>Супервизоров - ".$cc."</p>";
  else
   echo"<p align='center'>Экспертов - ".$cc."</p>";

  include "bottomadmin.php";
} else die;  
?>