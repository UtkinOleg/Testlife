<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {
  include "config.php";

  function GetCnt($mysqli, $kid)
  {
    $groups = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' LIMIT 1;");
    $groupsd = mysqli_fetch_array($groups);
    $cnt = $groupsd['count(*)'];
    mysqli_free_result($groups);
    return $cnt;
  }
  $kid = $_GET["id"];
  if (GetCnt($mysqli, $kid)==0)
  {
   $query = "DELETE FROM knowledge WHERE id=".$kid;
   if(mysqli_query($mysqli, $query))
   {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=knows'>\n";
      print "</HEAD></HTML>\n";
   } else {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=knows'>\n";
      print "</HEAD></HTML>\n";
   }
  } else {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=knows'>\n";
      print "</HEAD></HTML>\n";
  }
} else {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=knows'>\n";
      print "</HEAD></HTML>\n";
};  
?>