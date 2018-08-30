<?php
if(defined("IN_ADMIN")) {  
  
  require_once "config.php";  

  $type = $_GET["t"];
  if (empty($type))
   $type = 'supervisor';

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
    <link href="css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/dataTables.responsive.css" rel="stylesheet">    
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="lms/scripts/myboot.js?v=<?=$version?>"></script>
  </head>
<body>
      
<div id="spinner"></div>

<div class="modal fade" id="DelSupervisor" tabindex="-1" role="dialog" aria-labelledby="DelSupervisorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="DelSupervisorLabel">Удаление супервизора</h4>
      </div>
      <div class="modal-body">
       <form role="form">
        <input type="hidden" id="DelSupervosorId" value="">
       </form>
       Вы действительно хотите удалить (пользователя) супервизора?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="formDelSupervisor();">Да</button>
        <button type="button" class="btn btn-primary" onclick="$('#DelSupervisor').modal('hide');">Нет</button>
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
            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            <?= (!empty($type)) ? $type : "Супервизоры"?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper" id="resultTable">
                                <table class="table table-striped table-bordered table-hover" id="dT">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><i title='Супервизор' class='fa fa-user fa-fw'></i></th>
                                            <th></th>
                                            <th></th>
                                            <th><i title='Группа' class='fa fa-users fa-fw'></i></th>
                                            <? if ($type == 'supervisor') {?>
                                            <th><i title='Всего групп вопросов' class='fa fa-question fa-fw'></i></th>
                                            <th><i title='Всего тестов создано' class='fa fa-dashboard fa-fw'></i></th>
                                            <th><i title='Количество результатов тестирования' class='fa fa-bar-chart fa-fw'></i></th>
                                            <th><i title='Количество доступных сеансов тестирования' class='fa fa-bar-chart-o fa-fw'></i></th>
                                            <?}?>
                                            <th><i title='Количество проведенных экспертиз' class='fa fa-check fa-fw'></i></th>
                                            <th><i title='Дата регистрации' class='fa fa-clock-o fa-fw'></i></th>
                                            <? if(defined("IN_ADMIN")) echo "<th></th>";?>
                                        </tr>
                                    </thead>
                                </table>
                            </div>   
                        </div>
                   </div>              
                </div>
            </div>
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript">
  	  var table;
      function closeFancybox(){
       $.fancybox.close();
       table.ajax.reload();
      }    
	  	$(document).ready(function() {
		  	$('.fancybox').fancybox();
        table = $('#dT').DataTable({
         "ajax" : {
                'type': 'POST',
                'url': "getsupervisors.json",
                'data': {
                  <?=(!empty($type))?"t:'".$type."',":""?>
                }
         },
         responsive: true,
         "columns": [
           null,
           { "width": "30%" },
           { "width": "20%" },
           { "width": "20%" },
           { "width": "20%" },
           <? if ($type == 'supervisor') {?>
           { "searchable": false, "orderable": false }, 
           { "searchable": false, "orderable": false }, 
           { "searchable": false, "orderable": false }, 
           { "searchable": false, "orderable": false }, 
           <?}?>
           null, 
           { "searchable": false, "orderable": false },
           { "searchable": false, "orderable": false }
         ]          
        });
      });
      
      function formDelSupervosor() {
       var postParams = {
          id: $('#DelSupervosorId').val()
         }; 
       $('#DelSupervosor').modal('hide'); 
       $("#spinner").fadeIn("slow"); 
       $.post("delsupervosor.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
           table.ajax.reload();
          else 
           myInfoMsgShow("Ошибка при удалении супервизора!");
       });
      }
   </script>                        
  </body>
</html>

<?
} else die;  
?>            