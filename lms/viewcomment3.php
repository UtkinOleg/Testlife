<?php
  include "config.php";
  include "func.php";
  $pid = $_GET["id"];
  $title=$titlepage="Комментарии экспертов к проекту №".$pid;
  $helppage='';
  include "topadmin5.php";
  echo viewc($mysqli, $pid, $upload_dir);
  echo "</body></html>";
?>