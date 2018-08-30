<?

  $title = "Опубликованные проекты";
  $titlepage=$title;  

  include "config.php";
  include "func.php";

  // Выводим шапку страницы
  include "topadmin.php";


?>
<script type="text/javascript">
	$(document).ready(function() {
			$('.fancybox').fancybox();
		});
</script>    
<?

  echo"<p align='center'><div id='container'>";
  
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

  while($member = mysql_fetch_array($gst))
  {


 ?>

<script type="text/javascript">
		$(document).ready(function() {
    	$("#pub<? echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'opened&paid=<? echo $member['id']; ?>',
					type : 'iframe',
          width : 1000,
					padding : 5
				});
			});
    });  
</script>
<?

    $tot2z = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."' AND status='published'");
    $tot2zcnt = mysql_fetch_array($tot2z);
    $count2z = $tot2zcnt['count(*)'];

    if ($count2z>0) {

    echo "<div class='menu_glide_tops'>";
    echo "<table border='0'>";
    echo "<tr><td>";

    if (!empty($member['photoname']))
     {      
       if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'  class='leftimg'><div>"; 
     }

    echo "<p><h3><a id='pub".$member['id']."' href='javascript:;'>".$member['name']."</a></h3></p>";

    echo "<p>".$member['comment']."</p>
    <p align='center'><font size='-2'>Активность с <b>".data_convert ($member['startdate'], 1, 0, 0)."</b> по 
    <b>".data_convert ($member['stopdate'], 1, 0, 0)."</b>";
    echo " | <b>".$count2z."</b> опубликованных проект(ов)</p>";


   echo "</td></tr>"; 
   echo "</table></div>";
   }
  }

  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  if ($start > 0)  print " <p><A href='public&sort=".$sort."&start=".($start - $pnumber)."'>Предыдущие</A> ";
  if ($count > $start + $pnumber)  print " <p><A href='public&sort=".$sort."&start=".($start + $pnumber)."'>Следующие</A> \n";

  echo "</div></p>";

  include "bottomadmin.php";
?>