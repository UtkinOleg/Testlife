<?php
  if(!defined("IN_ADMIN")) die;  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
//  require_once "/home/siber113/php/Net/GeoIP.php";
  $title=$titlepage="Журнал работы";
  
  // Выводим шапку страницы
  include "topadmin.php";

  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  $tot = mysql_query("SELECT count(*) FROM logs;");
  $gst = mysql_query("SELECT * FROM logs ORDER BY logdate DESC LIMIT $start, $pnumber;");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table align="center" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='100'><p class=help>№</p></td>
              <td witdh='300'><p class=help>Сообщение</p></td>
              <td witdh='300'><p class=help>Автор</p></td>
              <td witdh='100'><p class=help>IP</p></td>
              <td witdh='100'><p class=help>Дата и время</p></td>
          </tr>   
     <?         

  $i=$start;
  while($member = mysql_fetch_array($gst))
  {
    $i = $i+1;
    echo "<tr><td witdh='100'><p class=help>".$i."</p></td>";
    echo "<td width='300'><p class=zag2>".$member['info'];
    ?>
    &nbsp;<a href="#" onClick="DelWindow(<? echo $member['id'];?> ,<? echo $start;?>,'dellog','logs','лог')" title="Удалить"><img src="img/b_drop.png" width="16" height="16"></a></p>
    <?
    
    echo "</td><td width='300'><a class='menu' href='edituser&utype=user&id=".$member['userid']."&start=$start'>".$member['userfio']."</a></td>";
    echo "<td witdh='100'><a class='menu' target='_blank' href='getipinfo.php?ip=".$member['ip']."'>".$member['ip']."</a></td>";
    echo "<td witdh='100'><p class='menu'>".data_convert ($member['logdate'], 1, 1, 0)."</p></td></tr>";
  }   
  
  echo "</table></div></p>";
    
  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
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
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='logs&start=<? echo $start2; ?>'">&nbsp;
     <?
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";
  
  //if ($start > 0)  echo "<p><A href=index.php?op=logs&start=".($start - $pnumber).">Предыдущие</A>";
  //if ($count > $start + $pnumber)  echo "<p><A href=index.php?op=logs&start=".($start + $pnumber).">Следующие</A>\n";
    
  include "bottomadmin.php";
?>