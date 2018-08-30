<?php
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
  {
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  $mode = $_GET["mode"];

  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  $title=$titlepage="Сводная статистика по экспертам";
  
  include "topadmin.php";

  $res5=mysql_query("SELECT * FROM projectarray WHERE id='".$selpaid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysql_fetch_array($res5);
  $openproject = $proarray['openproject'];
  if((defined("IN_SUPERVISOR") and $proarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {       


  $ex = mysql_query("SELECT count(*) FROM expertcontentnames WHERE proarrid='".$selpaid."' ORDER BY id");
  if (!$ex) puterror("Ошибка при обращении к базе данных");
  $excount = mysql_fetch_array($ex);
  $allexcount = $excount['count(*)'] + 1;
   
  $totmem = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized')");
  $total = mysql_fetch_array($totmem);
  $allcount = $total['count(*)'] * $allexcount;

    ?>
      <table align='center' class=bodytable width="90%" border="0" cellpadding=5 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr class=tableheader>
              <td width="50"><p class=help>№</p></td>
              <td width="500"><p class=help>Эксперт</p></td>
              <td width="100"><p class=help>Введено листов</p></td>
              <td width="100"><p class=help>Осталось листов</p></td>
          </tr>   
    <?


  $i=0;
  $s1=0;
  $s2=0;
  $res = mysql_query("SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
  while($r = mysql_fetch_array($res))
  {
//    $tot = mysql_query("SELECT count(*) FROM shablondb WHERE userid='".$r['expertid']."'");
    $tot = mysql_query("SELECT count(s.ball) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND s.userid='".$r['expertid']."'");
    $total = mysql_fetch_array($tot);
    $count = $total['count(s.ball)'];
    $allcnt = $allcount - $count;
    if ($allcnt<0) $allcnt=0;
    $s1 = $s1 + $count; 
    $s2 = $s2 + $allcnt; 
  
    if (!$tot) puterror("Ошибка при обращении к базе данных");

    $i=$i+1;
    
    $res3=mysql_query("SELECT * FROM users WHERE id='".$r['expertid']."'");
    $r3 = mysql_fetch_array($res3);
    
    echo "<tr>";
    echo "<td width='50'>".$i."</td>";
    echo "<td width='500'><p class=zag2>".$r3['userfio'];
    echo "</td>";
    echo "<td width='100' align='center'><p>".$count."</p></td>";
    echo "<td width='100' align='center'><p>".$allcnt."</p></td>";
  }

  echo "<tr>";
  echo "<td width='50'></td>";
  echo "<td width='500'><p>Итого</p></td>";
  echo "<td width='100' align='center'>".$s1."</td>";
  echo "<td width='100' align='center'>".$s2."</td>";
  echo "</table></p><br>";

  include "bottomadmin.php";
  } 
  }
  else die;
?>

