<?php
  include "config.php";
  include "func.php";
  $qgid = $_GET["qgid"];
  $title=$titlepage="Просмотр группы вопросов №".$qgid;
  $helppage='';
  include "topadmin5.php";
  echo viewqgroup($qgid);
  echo "</td></tr></table></td></tr></table></body></html>";
?>