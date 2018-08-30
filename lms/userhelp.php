<?php
if(defined("IN_SUPERVISOR")) {  

  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }
  
  require_once "config.php";  

?>

<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание тестов онлайн">
    <link rel="icon" href="ico/favicon.ico">
    <title>Test Life</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="lms/scripts/myboot.js?v=<?=$version?>"></script>
  </head>
<body>
      
<div id="spinner"></div>

<div id="wrapper">
<?php
  include "allnavigation.php";
  include "reminder.php";
  
  $id = $_GET['id'];
  if (empty($id))
  {
  $news = $_GET['t'];
  if ($news=='n')
  {
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages WHERE news=1 ORDER BY id DESC;");
  while ($member = mysqli_fetch_array($sql))
  {
?>
            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                        <?=$member['name']." от ".data_convert ($member['regdate'], 1, 0, 0)?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <?=$member['content']?>
                        </div>
                   </div>              
                </div>
            </div>
  <?}?>          
      </div>
    </div>
<?
  mysqli_free_result($sql);
  }
  }
  else
  {
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages WHERE id=".$id);
  $member = mysqli_fetch_array($sql);
?>
            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                        <?=$member['name']?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <?=$member['content']?>
                        </div>
                   </div>              
                </div>
            </div>
      </div>
    </div>
<?
  mysqli_free_result($sql);
  }
?>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="lms/scripts/myhelp.js?v=<?=$version?>"></script>
    <script type="text/javascript">
	  	$(document).ready(function() {
		  	$('.fancybox').fancybox();
	  	});
      function closeFancybox(){
       $.fancybox.close();
      }    
   </script>                        
  </body>
</html>

<?
} else die;  
?>            