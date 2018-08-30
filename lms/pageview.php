<?
  include "config.php";
  include "func.php";

  $pid = $_GET["id"];
  $gst = mysql_query("SELECT * FROM news WHERE id='".$pid."'");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $member = mysql_fetch_array($gst);
  $title = $member['name'];
  $titlepage=$title;  
  include "topadmin.php";
  
  echo "<table  width='100%'' border='0' cellpadding=0 cellspacing=0>";
  echo "<tr><td>";
  echo "
  <table border='0' cellpadding='3' cellspacing='3' width='100%'>
  <tr valign='top' align='left'><td valign='top'>";

  if (!empty($member['picurl']))
   echo "<img src=".$member['picurl']." width='140' class='leftimg'>"; 

  if ($member['pagetype']=='page')
   echo htmlspecialchars_decode($member['content']);
  else
   echo htmlspecialchars_decode($member['content2']);

  include "social.php";
  echo "</td></tr></table></td></tr>";
  $from = mysql_query("SELECT * FROM users WHERE id='".$member['userid']."'");
  $fromuser = mysql_fetch_array($from);

  if (!USER_REGISTERED)
   echo "<tr><td><p><font size='-2'>Страница опубликована ".$fromuser['userfio']." ".data_convert ($member['ndate'], 1, 0, 0)."</font></p></td></tr>";
  else
   echo "<tr><td><p><font size='-2'>Страница опубликована <a href='viewuser&id=".$member['userid']."'>".$fromuser['userfio']."</a> ".data_convert ($member['ndate'], 1, 0, 0)."</font></p></td></tr>";

  echo "</table>";
  
  include "bottomadmin.php";
?>