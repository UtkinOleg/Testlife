<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  
   require_once "config.php";  

   $tid = $_GET['id'];
   $kid = $_GET['kid'];
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

<div class="modal fade" id="DelPsy" tabindex="-1" role="dialog" aria-labelledby="DelPsyLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="DelPsyLabel">Удаление интерпретации</h4>
      </div>
      <div class="modal-body">
       <form role="form">
        <input type="hidden" id="DelPsyhiddenInfoId" value="">
        <input type="hidden" id="DelPsyhiddenInfoTestId" value="">
       </form>
       Вы действительно хотите удалить интерпретацию?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="formDelPsy();">Да</button>
        <button type="button" class="btn btn-primary" onclick="$('#DelPsy').modal('hide');">Нет</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="myInfoMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWarning" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabelWarning">Сообщение системы</h4>
      </div>
      <div id="myInfoMsgContent" class="modal-body">
      </div>
    </div>
  </div>
</div>

<div id="wrapper">
<?php
  include "allnavigation.php";
  include "reminder.php";
?>
         <div id="psy">
         </div>
         <p></p>
         <button type="button" class="btn btn-outline btn-primary btn-sm" onclick="location.href='qt&m=t&kid=<?=$kid?>'"><i class="fa fa-dashboard fa-fw"></i> Назад</button>
         &nbsp;<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="dialogOpen('editpsitest&m=a&tid=<?=$tid?>',0,0)"><i class="fa fa-comment-o fa-fw"></i> Добавить новую</button>
      </div>
    </div>
    
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
        getpsy(<?=$tid?>,<?=$kid?>);
	  	});
      function closeFancyboxAndRedirectToUrl(){
       $.fancybox.close();
       getpsy(<?=$tid?>,<?=$kid?>);
      }    
      function closeFancybox(){
       $.fancybox.close();
      }    
   </script>                        
  </body>
</html>

<?
} else die;  
?>            