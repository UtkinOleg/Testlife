<?
  include "config.php";
  include "func.php";

  $pid = $_GET["id"];
  $gst = mysql_query("SELECT * FROM news WHERE id='".$pid."' LIMIT 1;");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $member = mysql_fetch_array($gst);
  $title = $member['name'];
  $titlepage=$title;  
  
  require_once "header.php"; 
  echo"</head><body><center><p>";
  echo "<font face='Tahoma, Arial' size='+1'>".$title."</font></p>";
    
  echo "<table width='100%'' border='0' cellpadding=0 cellspacing=0>";
  echo "<tr><td>";
  echo "
  <table border='0' cellpadding='3' cellspacing='3' width='100%'>
  <tr valign='top' align='left'><td valign='top'>";

  if (!empty($member['picurl']))
   echo "<img src=".$member['picurl']." width='140' class='leftimg'>"; 

  if ($member['pagetype']=='page')
   echo htmlspecialchars_decode($member['content']);
  else
  {
   if (empty($member['content2']))
    echo htmlspecialchars_decode($member['content']);
   else
    echo htmlspecialchars_decode($member['content2']);
  }
  
  echo "</td></tr></table></td></tr>";
  echo "</table>";
  
  echo "</center><p></p></body></html>";
?>