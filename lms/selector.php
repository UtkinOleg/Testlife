<?
if (defined("IN_SUPERVISOR") or defined("IN_EXPERT")) 
{

include "config.php";
include "func.php";

$m = $_GET["m"];
  
require_once "header.php"; 
?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script language="javascript">

  function projects(paid) 
  { 
   parent.closeFancyboxAndRedirectToProjects(paid);
  } 
  function lists(paid) 
  { 
   parent.closeFancyboxAndRedirectToLists(paid);
  } 
 
	$(document).ready(function() {
      $("button").button();
  });
 
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>


<?
  if ($m=='p')
   echo "<h3 class='ui-widget-header ui-corner-all' align='center'><p>Проекты участников</p></h3>";
  else 
  if ($m=='e')
   echo "<h3 class='ui-widget-header ui-corner-all' align='center'><p>Экспертиза проектов</p></h3>";

  if (defined("IN_ADMIN"))
   $tot = mysql_query("SELECT a.* FROM projectarray as a WHERE a.closed=0 ORDER BY a.id DESC");
  else
  {
    if ($m=='e')
      $tot = mysql_query("SELECT a.* FROM projectarray as a, proexperts as e WHERE a.closed=0 AND a.id=e.proarrid AND e.expertid='".USER_ID."' AND a.openexpert=0 ORDER BY a.id DESC");
    else
      $tot = mysql_query("SELECT a.* FROM projectarray as a, proexperts as e WHERE a.closed=0 AND a.id=e.proarrid AND e.expertid='".USER_ID."' ORDER BY a.id DESC");
  } 

  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $bb=0;
  while($member = mysql_fetch_array($tot))
  {

    $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."'");
    $tot2cnt = mysql_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];

    if ($count2>0) 
    {
    $bb++;
    echo "<div class='menu_glide_tops'>";
    echo "<table border='0'>";
    echo "<tr><td>";

   
    if (!empty($member['photoname']))
     {      
       if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' width='70' class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' width='70' class='leftimg'><div>"; 
     }
     
    if ($m=='p')
     echo "<button title='Посмотреть проекты участников' onclick='parent.closeFancyboxAndRedirectToProjects(".$member['id'].")'><i class='fa fa-book fa-lg'></i> ";
    else
    if ($m=='e')
     echo "<button title='Экспертиза проектов' onclick='parent.closeFancyboxAndRedirectToLists(".$member['id'].")'><i class='fa fa-thumbs-o-up fa-lg'></i> ";
//    echo "<p><h3><font face='Tahoma,Arial'>".$member['name']."</font></h3></p>";
    echo $member['name'];
    echo "</button>";

    echo "<p>".$member['comment']."</p>
    <p><font size='-2'>Активность с <b>".data_convert($member['startdate'], 1, 0, 0)."</b> по 
    <b>".data_convert($member['stopdate'], 1, 0, 0)."</b>";
    echo " | <b>".$count2."</b> проект(ов)</font></p>";

    echo "</td></tr>"; 
    echo "</table></div>";
    }
  }
  if ($bb==0)
{
?>

            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Нет проектов для экспертизы.</p>
            	</div>
            </div><p></p> 
 <? 
     }  
?>
</td></tr></table>
</body></html>
<?
} else die;
?>