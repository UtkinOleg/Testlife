<?
  $title = "Рейтинги открытых проектов";
  $titlepage=$title;  

  include "config.php";
  include "func.php";

  // Выводим шапку страницы
  include "topadmin.php";

  echo"<p align='center'>";
  
  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  $sort = $_GET["sort"];
  if (empty($sort)) $sort='id';
   
  $tot = mysql_query("SELECT count(*) FROM projectarray WHERE openproject=1");
  $gst = mysql_query("SELECT * FROM projectarray WHERE openproject=1 ORDER BY ".$sort." DESC LIMIT $start, $pnumber;");
  if (!$gst|| !$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white align=center>
          <tr <? echo $tableheader ?> align='center'>
              <td witdh='100'><p>№</p></td>
              <td witdh='300'><p><a href='tops&sort=name'>Наименование</a></p></td>
              <td witdh='200'><p>Период действия</p></td>
              <td witdh='100'><p>Количество проектов</p></td>
              <td witdh='100'><p>Количество экспетров</p></td>
          </tr>   
     <?         

  $i=$start;
  while($member = mysql_fetch_array($gst))
  {
    $i=$i+1;
    $ii = $i/2;
    $k = substr($ii, strpos($ii,'.')+1);
    if (empty($k))
     echo "<tr bgcolor='#FFFFFF'>";
    else
     echo "<tr>";
    
    $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."'");
    $tot2cnt = mysql_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];

    echo "<td witdh='100'><p align='center'>".$i."</p></td>";
    echo "<td width='300'>";
    if ($count2>0) {
     ?>
     <p align="center"><input type="button" name="viewproject" value="<? echo $member['name']; ?>" onclick="document.location='<? echo $site; ?>/report2&mode=0&paid=<? echo $member['id']; ?>'"></p>
     <?
    }
    else
    {
     echo "<p align='center'>".$member['name']."</p>";
    } 
    echo "<p align='center'><font face='Tahoma, Arial' size='-2'>".$member['comment']."</font></p></td><td width='200'><p align='center'>".data_convert ($member['startdate'], 1, 0, 0)." - ".data_convert ($member['stopdate'], 1, 0, 0)."</p></td>";
    
    echo "<td width='100'><p align='center'>".$count2."</p></td>";

    $tot3 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$member['id']."'");
    $tot3cnt = mysql_fetch_array($tot3);
    $count3 = $tot3cnt['count(*)'];
    echo "<td width='100'><p align='center'>".$count3."</p></td>";

   echo "</tr>"; 
  }
  echo "</table></div></p>";

  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  if ($start > 0)  print " <p><A href='tops&sort=".$sort."&start=".($start - $pnumber)."'>Предыдущие</A> ";
  if ($count > $start + $pnumber)  print " <p><A href='tops&sort=".$sort."&start=".($start + $pnumber)."'>Следующие</A> \n";

  include "bottomadmin.php";
?>