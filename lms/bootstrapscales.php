<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 
  
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

<div class="modal fade" id="DelScale" tabindex="-1" role="dialog" aria-labelledby="DelScaleLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="DelScaleLabel">Удаление шкалы оценок</h4>
      </div>
      <div class="modal-body">
       <form role="form">
        <input type="hidden" id="DelScalehiddenInfoId" value="">
       </form>
       Вы действительно хотите удалить шкалу оценок?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="formDelScale();">Да</button>
        <button type="button" class="btn btn-primary" onclick="$('#DelScale').modal('hide');">Нет</button>
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

  if (defined("IN_ADMIN")) 
   $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM scales LIMIT 1;");
  else
  if (defined("IN_SUPERVISOR")) 
   $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM scales WHERE ownerid='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($countg); 

  if ($count_gs==0)
  {
?>
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Стандартная</strong> шкала оценок, установленная в системе - процент набранных баллов до 45% - оценка <span class="badge" style="background-color:#F20909;">2</span>, от 45% до 69% - оценка <span class="badge" style="background-color:#ED9C09;">3</span>, от 70% до 85% - оценка <span class="badge" style="background-color:#153FE4;">4</span>, от 86% - оценка <span class="badge" style="background-color:#29B004;">5</span>. Для изменения стандартной шкалы оценок, необходимо ввести <a href="javascript:;" onclick="dialogOpen('edscale&amp;m=a',0,0)">новую шкалу</a>.
          </div>
<?}?>

            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            Шкалы оценок
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                           <p>
                            <button type="button" class="btn btn-outline btn-primary btn-sm" onclick="dialogOpen('edscale&m=a',0,0)"><i class="fa fa-arrows-h fa-fw"></i> Новая шкала оценок</button>
                           </p>
                           <div id="scales"></div>
                        </div>
                   </div>              
                </div>
            </div>
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script type="text/javascript">
	  	$(document).ready(function() {
		  	$('.fancybox').fancybox();
        getscales();
	  	});
      function closeFancybox(){
       $.fancybox.close();
       getscales();
      }    
   </script>                        
  </body>
</html>

<?
} else die;  
            