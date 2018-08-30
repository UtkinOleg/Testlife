<?php
if(defined("IN_ADMIN")) {
  include "config.php";
  $query = "DELETE FROM limitsupervisor WHERE id=".$_GET["id"];
  if(mysqli_query($mysqli, $query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=admlimit'>\n";
      print "</HEAD></HTML>\n";
  }
} else die;  
?>