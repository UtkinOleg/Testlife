<?php
 if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT")) 
 {
  include "config.php";
  include "func.php";
  $paid = $_GET["paid"];
  $listid = $_GET["listid"];
  $exlistid = $_GET["exlistid"];
  $listball = $_GET["listball"];
  $title=$titlepage="Экспертный лист №".$listid;
  $helppage='';
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
</style>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Просмотр экспертного листа</p></h3>
<div id="menu_glide" class="menu_glide">
<?
  echo viewlist($mysqli, $paid, $listid, $listball, $exlistid);   
  echo "</div></td></tr></table></body></html>";
 } 
?>