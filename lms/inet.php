<?
  $title = "Голосование";
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
   
  $tot = mysql_query("SELECT count(*) FROM projectarray WHERE enableinet=1");
  $gstt = mysql_query("SELECT * FROM projectarray WHERE enableinet=1 ORDER BY ".$sort." DESC LIMIT $start, $pnumber;");
  if (!$gstt||!$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white align=center>
     <?         

  $i=$start;
  while($member = mysql_fetch_array($gstt))
  {
    $i=$i+1;
    $ii = $i/2;
    $k = substr($ii, strpos($ii,'.')+1);
    if (empty($k))
     echo "<tr bgcolor='#FFFFFF'>";
    else
     echo "<tr>";
    
    $count2=0;
    $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."' AND status!='created'");
    $tot2cnt = mysql_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];

//    echo "<td witdh='100'><p align='center'>".$i."</p></td>";
    echo "<td width='300'>";

    // Начнем сравнение дат
    $date1 = $member['startdate'];
    $date2 = $member['stopdate'];
    $date3 = date("d.m.Y");
    preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$iu);
    $day=$iu[1][0];
    $month=$iu[2][0];
    $year=$iu[3][0];
    
    $arr1 = explode(" ", $date1);
    $arr2 = explode(" ", $date2);  

   $arrdate1 = explode("-", $arr1[0]);
   $arrdate2 = explode("-", $arr2[0]);


    $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
    $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
    $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));

    $enablevote = 0; 
    if (($timestamp3 >= $timestamp1) and ($timestamp3 <= $timestamp2)) 
    {
     if ($count2>0) {
      ?>
      <p align="center"><font size="+1"><input type="button" name="viewproject" value="<? echo $member['name']; ?>" onclick="document.location='<? echo $site; ?>/dovote&paname=<? echo $member['name']; ?>&paid=<? echo $member['id']; ?>'"></font></p>
      <?
    } 
     else
      echo "<p align='center'>".$member['name']."</p>";
    }
    else
      echo "<p align='center'>".$member['name']."</p>";
    
    echo "<p align='center'><font face='Tahoma, Arial' size='-2'>".$member['comment']."</font></p></td><td width='200'><p align='center'>с ".data_convert ($member['startdate'], 1, 0, 0)." по ".data_convert ($member['stopdate'], 1, 0, 0)."</p></td>";
    
//    echo "<td width='100'><p align='center'>".$count2."</p></td>";
    if ($count2>0) $enablevote = 1;

    $height = $count2*100;
    if ($height>500) $height=500;
    
    $tot3 = mysql_query("SELECT up,down FROM projects WHERE proarrid='".$member['id']."' AND status!='created'");
    $count3 = 0;
    while($tot3cnt = mysql_fetch_array($tot3))
     $count3 += $tot3cnt['up']+$tot3cnt['down'];

//    echo "<td width='100'><p align='center'>".$count3."</p></td>";

?>
<script type='text/javascript'>
		$(document).ready(function() {
    	$('#fancybox<?php echo $member['id']; ?>').click(function() {
				$.fancybox.open({
					href : 'voteres&paid=<? echo $member['id'] ?>',
					type : 'iframe',
          autoscale : false, 
          autoSize : false,           
          height : <? echo $height ?>,
					padding : 5
				});
			});
		});
</script>
<?
  if ($enablevote==1)
   echo " <td><font size='-1'>
    <a title='Результаты голосования (всего голосов ".$count3.")' id='fancybox".$member['id']."' href='javascript:;'>".$member['info']."<img src='img/stat.gif'></a>
    </font></td></tr> ";
  else  
   echo " <td><font size='-1'>
    <img src='img/statoff.gif'>
    </font></td></tr> ";
  
  }
  echo "</table></div></p>";

  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  if ($start > 0)  print " <p><A href='inet&sort=".$sort."&start=".($start - $pnumber)."'>Предыдущие</A> ";
  if ($count > $start + $pnumber)  print " <p><A href='inet&sort=".$sort."&start=".($start + $pnumber)."'>Следующие</A> \n";

  include "bottomadmin.php";
?>