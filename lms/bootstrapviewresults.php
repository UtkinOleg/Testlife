<?php
if(!defined("USER_REGISTERED")) die;

  require_once "config.php";  

  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }
  
  $testid = $_GET["tid"];
  $bdate = $_GET["bdate"];
  $edate = $_GET["edate"];
  $userid = $_GET["uid"];
  $groupid = $_GET["grid"];
  $folderid = $_GET["frid"];
  $folder_parent_id = $_GET["frpid"];
  
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
   $userid = $_GET["uid"];
  else
  if (defined("IN_USER"))
   $userid = USER_ID;
  else
   die;

  $href = "";
  if (empty($userid))
   $href .= "&uid=".$userid;
  if (!empty($testid))
   $href .= "&tid=".$testid;
  if (!empty($groupid))
   $href .= "&grid=".$groupid;
  
  $selector = false;
  if (!empty($testid)) { 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM testgroups WHERE signature='".$testid."' LIMIT 1;");
   if (!$sql) die;
   $data = mysqli_fetch_array($sql);
   $tname = $data['name']; 
   mysqli_free_result($sql);
   $selector = true;
  }  

  if (empty($bdate))
   $bdate = date('d.m.Y');
  
  if (empty($edate))
   $edate = date('d.m.Y');
  
  if (!empty($groupid)) { 
   if(defined("IN_ADMIN"))
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$groupid."' LIMIT 1;");
   else
   if(defined("IN_SUPERVISOR"))
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$groupid."' AND userid='".USER_ID."' LIMIT 1;");
   if (!$sql) { die; }
   $data = mysqli_fetch_array($sql);
   $gname = $data['name']; 
   mysqli_free_result($sql);
   $selector = true;
  }  

  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid)) { 
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM folders WHERE id='".$folderid."' LIMIT 1;");
    if (!$sql) { die; }
    $data = mysqli_fetch_array($sql);
    $fname = $data['name']; 
    mysqli_free_result($sql);
    $selector = true;
   }
   else
   if (!empty($folder_parent_id)) { 
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM folders WHERE id='".$folder_parent_id."' LIMIT 1;");
    if (!$sql) { die; }
    $data = mysqli_fetch_array($sql);
    $fname = $data['name']; 
    mysqli_free_result($sql);
    $selector = true;
   }
  }  
  
  if (!empty($userid)) { 
   if ($userid==USER_ID)
    $uname = USER_FIO; 
   else
   {
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$userid."' LIMIT 1;");
    if (!$sql) die;
    $data = mysqli_fetch_array($sql);
    $uname = $data['userfio']; 
    mysqli_free_result($sql);
   }
   $selector = true;
  }  


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
    <link href="css/morris.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  </head>
  <style>
.poll__answer__item_bar-wrapper {
  float: none;
  width: 100%;
}  
.poll__answer__item_bar {
  display: block;
  position: relative;
  float: left;
  height: 20px;
  margin-right: 5px;
  background-color: #838EFA;
}
  </style>
  <body>
   <div id="spinner"></div>

<?  if(defined("IN_ADMIN")) {?>

<div class="modal fade" id="DelResult" tabindex="-1" role="dialog" aria-labelledby="DelResultLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="DelResultLabel">Удаление результата</h4>
      </div>
      <div class="modal-body">
       <form role="form">
        <input type="hidden" id="DelResulthiddenInfoId" value="">
       </form>
       Вы действительно хотите удалить результат тестирования?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="formDelResult();">Да</button>
        <button type="button" class="btn btn-primary" onclick="$('#DelResult').modal('hide');">Нет</button>
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
<?}?>
   
   <div id="wrapper">
<?php
  include "allnavigation.php";
  include "reminder.php";
?>

<div class="container-fluid"><div class="row">
   <div class="col-lg-4 col-md-6" id="sandbox-container">
    <div class="input-daterange input-group" id="datepicker">
     <input style="height: 34px; font-size: 14px;" type="text" class="input-sm form-control" name="bdate" id="bdate" value="<?=$bdate?>">
     <span class="input-group-addon">-</span>
     <input style="height: 34px; font-size: 14px;" type="text" class="input-sm form-control" name="edate" id="edate" value="<?=$edate?>">
     <span class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="location.href='vr<?=$href?>&bdate='+$('#bdate').val()+'&edate='+$('#edate').val()"><i class="fa fa-search fa-fw"></i>
      </button>
    </span>
   </div> 
</div></div>
                           <? if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
                                 <p></p>
                                 <? if (!empty($userid)) echo " Участник: <strong>".$uname."</strong>" ?>
                                 <? if (!empty($groupid)) echo " Группа: <strong>".$gname."</strong>" ?>
                                 <? if (!empty($folderid) or !empty($folder_parent_id)) echo " Папка: <strong>".$fname."</strong>" ?>
                                 <? if (!empty($testid)) echo " Тест: <strong>".$tname."</strong>" ?>
                                 <? if ($selector) echo '<button title="Сбросить фильтр" type="button" class="btn btn-primary btn-circle" onclick="location.href=\'vr\'"><i class="fa fa-times fa-fw"></i></button>'?>
                                 <p></p> 
                           <?}?>  
                                  
            <div class="row">
                <div class="col-lg-12">
                            <?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
                            <ul id="qtTab" style="margin-bottom: 10px;" class="nav nav-pills">
                                <li class="active"><a href="#results1" data-toggle="tab"><i class='fa fa-bar-chart-o fa-fw'></i> Результаты</a>
                                </li>
                                <li><a href="#results2" data-toggle="tab"><i class='fa fa-pie-chart fa-fw'></i> Произвольная диаграмма</a>
                                </li>
                                <li><a href="#results3" data-toggle="tab"><i class='fa fa-pie-chart fa-fw'></i> Освоение тем</a>
                                </li>
                                <li><a href="#results4" data-toggle="tab"><i class='fa fa-pie-chart fa-fw'></i> Решаемость заданий</a>
                                </li>
                                <li><a href="#results5" data-toggle="tab"><i class='fa fa-pie-chart fa-fw'></i> Плотность распределения баллов</a>
                                </li>
                                <? if(defined("IN_ADMIN")) { ?>
                                <li><a href="#results6" data-toggle="tab"><i class='fa fa-bar-chart fa-fw'></i> Анализ ответов</a>
                                </li>
                                <?}?>
                            </ul>
                             <?} else 
                             if(defined("IN_USER")) {
                             ?> 
                             <h3>Мои результаты</h3>  
                             <?}?>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="results1">
                            <div class="dataTable_wrapper" id="resultTable">
                                <table class="table table-striped table-bordered table-hover" id="dT">
                                    <thead>
                                        <tr>
                                            <th></th>
                                        <?   if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
                                            <th><i title='Участник' class='fa fa-user fa-fw'></i></th>
                                            <th><i title='Группа' class='fa fa-users fa-fw'></i></th>
                                            <th></th>
                                        <? } ?>    
                                            <th><i title='Тест' class='fa fa-dashboard fa-fw'></i></th>
                                            <th><i title='Всего вопросов' class='fa fa-question fa-fw'></i></th>
                                            <th><i title='Правильно отвечено' class='fa fa-check fa-fw'></i></th>
                                            <th><i title='Баллов получено' class='fa fa-calculator fa-fw'></i></th>
                                            <th>%</th>
                                            <th><i title='Оценка' class='fa fa-star fa-fw'></i></th>
                                            <th><i title='Дата и время' class='fa fa-clock-o fa-fw'></i></th>
                                            <th></th>
                                            <? if(defined("IN_ADMIN")) echo "<th></th>";?>
                                        </tr>
                                    </thead>
                                </table>
                            </div>   
              <?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
                            <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('resultTable')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                            <button type="button" class="btn btn-outline btn-primary" onclick="document.location.href='res&tid=<?=$testid?>&bdate=<?=$bdate?>&edate=<?=$edate?>&uid=<?=$userid?>&grid=<?=$groupid?>&frid=<?=$folderid?>&frpid=<?=$folder_parent_id?>';"><i class='fa fa-file-text-o fa-fw'></i> RTF</button>
              <?}?>
                                </div>
              <?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
                                <div class="tab-pane fade" id="results2">
                                     <form role="form" method="post">
                                        <div class="form-group">
                                            <label>Ось X</label>
                                            <select id="diag1x" class="form-control">
                                                <option value='0'>Имя</option>
                                                <option value='1'>Email</option>
                                                <option value='2'>Тест</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Ось Y</label>
                                            <select id="diag1y" class="form-control">
                                                <option value='3'>Правильных ответов</option>
                                                <option value='4'>Всего баллов</option>
                                                <option value='5' selected>Уровень знаний</option>
                                                <option value='6'>Все показатели</option>
                                            </select>
                                        </div>
                                      </form>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="diag1()"><i class='fa fa-pie-chart fa-fw'></i> Построить диаграмму</button>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('morris-area-chart1')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                                      <p></p>             
                                      <div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title"><i class="fa fa-pie-chart fa-fw"></i> Произвольная диаграмма</h3>
                                        </div>
                                        <div class="panel-body">
                                         <div id="morris-area-chart1"></div>
                                        </div>
                                      </div>                                      
                                </div>

                                <div class="tab-pane fade" id="results3">
                                <p></p> 
                                <? if (empty($testid)) {?> 
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Не выбран тест!</strong> Для построения диаграммы <strong>Освоение тем</strong> необходимо в результатах выбрать определенный тест.
          </div>
                                <?} else {
                                ?>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="diag2()"><i class='fa fa-pie-chart fa-fw'></i> Построить диаграмму</button>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('morris-area-chart2')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                                      <p></p>             
                                      <div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title"><i class="fa fa-pie-chart fa-fw"></i> Освоение тем (групп вопросов) в тесте <strong><?=$tname?></strong></h3>
                                        </div>
                                        <div class="panel-body">
                                         <div id="morris-area-chart2"></div>
                                        </div>
                                      </div>                                      
                                
                                <?}?> 
                                <p>Диаграмма позволяет отслеживать те группы вопросов (разделы, темы), по которым тестируемые имеют наилучшее усвоение.</p>    
                                <p>Значения коэффициентов освоения группы вопросов или дидактической единицы (ДЕ) дисциплины выражаются через долю тестируемых, преодолевших критерий освоения конкретной ДЕ дисциплины. В качестве критерия взято выполнение 100% заданий из их общего числа в ДЕ. Карта коэффициентов освоения ДЕ позволяет оценить степень освоения ДЕ и выявить разделы, освоенные на недостаточном уровне. Для группы тестируемых, освоивших дисциплину на уровне требований ГОС, коэффициенты освоения ДЕ должны быть не ниже 0,8.</p>
                                </div>

                                <div class="tab-pane fade" id="results4">
                                <p></p> 
                                <? if (empty($testid)) {?> 
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Не выбран тест!</strong> Для построения диаграммы <strong>Решаемость заданий</strong> необходимо в результатах выбрать определенный тест.
          </div>
                                <?} else {
                                ?>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="diag3()"><i class='fa fa-pie-chart fa-fw'></i> Построить диаграмму</button>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('morris-area-chart3')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                                      <p></p>             
                                      <div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title"><i class="fa fa-pie-chart fa-fw"></i> Решаемость заданий в тесте <strong><?=$tname?></strong></h3>
                                        </div>
                                        <div class="panel-body">
                                         <div id="morris-area-chart3"></div>
                                        </div>
                                      </div>                                      
                                
                                <?}?> 
                                <p>Диаграмма позволяет оценить уровень подготовки тестируемых по всем вопросам в выбранном тесте. При построении диаграммы учитывается случайный порядок выборки заданий в тестах.</p>
                                <p>Значения коэффициентов решаемости заданий рассчитываются как отношение числа испытуемых, решивших задание, к общему числу прошедших тестирование. При анализе результатов педагогических измерений по карте коэффициентов решаемости можно придерживаться следующей классификации уровней трудности заданий: лёгкие задания - коэффициент решаемости от 0,7 до 1,0, задания средней трудности - коэффициент решаемости от 0,4 до 0,7 и задания повышенной трудности - коэффициент решаемости менее 0,4. Для группы тестируемых, освоивших дисциплину на уровне требований ГОС, все задания должны иметь коэффициент решаемости не ниже 0,7.</p>    
                                </div>
                                
                                <div class="tab-pane fade" id="results5">
                                      <button type="button" class="btn btn-outline btn-primary" onclick="diag4()"><i class='fa fa-pie-chart fa-fw'></i> Построить диаграмму</button>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('morris-area-chart4')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                                      <p></p>             
                                      <div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title"><i class="fa fa-pie-chart fa-fw"></i> Плотность распределения баллов</h3>
                                        </div>
                                        <div class="panel-body">
                                         <div id="morris-area-chart4"></div>
                                        </div>
                                      </div>                                      
                                      <p>Диаграмма позволяет судить о характере распределения результатов для данной выборки тестируемых (групп тестируемых).</p>
                                      <p>Каждое деление на диаграмме показывает количество тестируемых, результаты которых лежат в данном 5-процентном интервале уровня знаний. По диаграмме определяется характер распределения результатов для данной группы тестируемых и могут быть выделены подгруппы тестируемых с различным уровнем подготовки. Для группы, освоивших дисциплину на уровне требований ГОС, диаграмма должна быть смещена в сторону высоких процентов выполненных заданий (т.е. большинство результатов - выше 70%).</p>
                                </div>
                                
                                <div class="tab-pane fade" id="results6">
                                <p></p> 
                                <? if (empty($testid)) {?> 
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Не выбран тест!</strong> Для построения <strong>Анализа ответов</strong> необходимо в результатах выбрать определенный тест.
          </div>
                                <?} else {
                                ?>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="diag5()"><i class='fa fa-bar-chart fa-fw'></i> Построить анализ</button>
                                      <button type="button" class="btn btn-outline btn-primary" onclick="xepOnline.Formatter.Format('morris-area-chart5')"><i class='fa fa-file-pdf-o fa-fw'></i> PDF</button>
                                      <p></p>             
                                      <div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title"><i class="fa fa-bar-chart fa-fw"></i> Анализ ответов в тесте <strong><?=$tname?></strong></h3>
                                        </div>
                                        <div class="panel-body">
                                         <div id="morris-area-chart5"></div>
                                        </div>
                                      </div>                                      
                                
                                <?}?> 
                                </div>
                               
                              <?}?> 
                        </div>
                </div>
            </div>
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/raphael.min.js"></script>
    <script src="js/morris.min.js"></script>
    <script type='text/javascript' src="http://xep.cloudformatter.com/doc/js/xepOnline.jqPlugin.008.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/bootstrap-datepicker.ru.js" charset="UTF-8"></script>
    <script src="js/sbadmin.js"></script>
    <script src="lms/scripts/myhelp.js?v=<?=$version?>"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script type="text/javascript">
	  	var table;
      $(document).ready(function() {
		  	$('.fancybox').fancybox();
        table = $('#dT').DataTable({
         "ajax" : {
                'type': 'POST',
                'url': "getresult.json",
                'data': {
                  <?=(!empty($testid))?"tid:'".$testid."',":""?>
                  <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
                  <?=(!empty($edate))?"edate:'".$edate."',":""?>
                  <?=(!empty($userid))?"uid:".$userid.",":""?>
                  <?=(!empty($groupid))?"grid:".$groupid.",":""?>
                  <?=(!empty($folderid))?"frid:".$folderid.",":""?>
                  <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
                }
         },
         responsive: true,
         "columns": [
           null,
<?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
           { "width": "30%" } ,
           { "width": "20%" },
           { "searchable": false, "orderable": false },
<?}?>
           { "width": "20%" },
           null,
           null,
           null,
           null,
           null,
           null,
           <? if(defined("IN_ADMIN")) {?> { "searchable": false, "orderable": false }, <?}?>
           { "searchable": false, "orderable": false }]          
        });
<?  if(defined("IN_ADMIN")) {?>
      //  setInterval( function () {
      //   table.ajax.reload();
      //  }, 30000 );
<?}?>
<?
  if(defined("IN_ADMIN"))
   $q = "SELECT MIN(s.resdate),MAX(s.resdate) FROM singleresult as s LIMIT 1;"; 
  else
  if(defined("IN_SUPERVISOR"))
   $q = "SELECT MIN(s.resdate),MAX(s.resdate) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." LIMIT 1;"; 
  else
  if(defined("IN_USER"))
   $q = "SELECT MIN(s.resdate),MAX(s.resdate) FROM singleresult as s WHERE s.userid=".USER_ID." LIMIT 1;"; 
  $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);
  $testdata = mysqli_fetch_array($res);
  $mindate = data_convert ($testdata['MIN(s.resdate)'], 1, 0, 0);
  $maxdate = data_convert ($testdata['MAX(s.resdate)'], 1, 0, 0);
  mysqli_free_result($res);
?>
       $('#sandbox-container .input-daterange').datepicker({
        format: "dd.mm.yyyy",
        startDate: "<?=$mindate?>",
        endDate: "<?=$maxdate?>",
        language: "ru"
       });
	  	});

    
<?  if(defined("IN_ADMIN")) {?>
    function formDelResult() {
     var d = ''+$('#DelResulthiddenInfoId').val();
     $('#DelResult').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delresult.json", {id:d}, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
            table.ajax.reload();
          else 
           myInfoMsgShow("Ошибка при удалении результата!");
       });
      }
<?}?>      
      function closeFancyboxAndRedirectToUrl(url){
       $.fancybox.close();
       location.replace(url);
      }    
      
      function closeFancybox(){
       $.fancybox.close();
      }  

      function dialogOpen(phref, pwidth, pheight) {
				if (pwidth==0)
         pwidth = document.documentElement.clientWidth;
				if (pheight==0)
         pheight = document.documentElement.clientHeight;
        $.fancybox.open({
					href : phref,
					type : 'iframe',
          width : pwidth,
          height : pheight,
          fitToView : true,
          autoSize : false,          
          modal : true,
          showCloseButton : false,
					padding : 5
				});
      }
      
      function getusermsgs() 
      {
       $.post('getusermsgs.json',{},  
        function(data){  
         eval('var obj='+data);         
         $('#usermsgsoper').prop("onclick",null);        
         $('#usermsgs').empty();  
         if(obj.ok=='1')
          $('#usermsgs').append(obj.content);        
         else 
          $('#usermsgs').append('Ошибка при загрузке сообщений.');        
        }); 
      }
      
<?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>

  function gettesttasks() 
  {
    $.post('gettesttasks.json',{},  
     function(data){  
      eval('var obj='+data);         
      $('#testtasks').empty();  
      if(obj.ok=='1')
       $('#testtasks').append(obj.content);        
      else 
       $('#testtasks').append('Ошибка при загрузке текущих сеансов.');        
    }); 
  }

      function diag1() {
        var xx = $("#diag1x").val();
        var yy = $("#diag1y").val();
        var name = $("#diag1y option:selected").text();
        $('#morris-area-chart1').html('');
        $("#spinner").fadeIn("slow"); 
        $.ajax({    
         url: 'diag1.json',
         type: 'POST',
         data: { 
          x:xx, 
          y:yy, 
          <?=(!empty($testid))?"tid:'".$testid."',":""?>
          <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
          <?=(!empty($edate))?"edate:'".$edate."',":""?>
          <?=(!empty($userid))?"uid:".$userid.",":""?>
          <?=(!empty($groupid))?"grid:".$groupid.",":""?>
          <?=(!empty($folderid))?"frid:".$folderid.",":""?>
          <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
         },
         dataType: "json",
         success: function(outEvoVal) {    
           //console.log(outEvoVal);
           var yy = $("#diag1y").val();
           $("#spinner").fadeOut("slow"); 
           if (yy==6)
           {
            Morris.Area({  
             element: 'morris-area-chart1',
             data: outEvoVal,
             xkey: 'x',
             ykeys: ['y','a','c'],
             labels: ['Правильных ответов','Всего баллов','Уровень знаний'],
             lineColors: ['#0000FF','#044c29','#F44c00'],
             lineWidth: 2,
             parseTime:false,
            }); 
           }
           else
           {
            Morris.Area({  
             element: 'morris-area-chart1',
             data: outEvoVal,
             xkey: 'x',
             ykeys: ['y'],
             labels: [name],
             lineColors: ['#0000FF','#044c29'],
             lineWidth: 2,
             parseTime:false,
            }); 
           }   
        }    
       });
      }

      function diag2() {
        $('#morris-area-chart2').html('');
        $("#spinner").fadeIn("slow"); 
        $.ajax({    
         url: 'diag2.json',
         type: 'POST',
         data: { 
          <?=(!empty($testid))?"tid:'".$testid."',":""?>
          <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
          <?=(!empty($edate))?"edate:'".$edate."',":""?>
          <?=(!empty($userid))?"uid:".$userid.",":""?>
          <?=(!empty($groupid))?"grid:".$groupid.",":""?>
          <?=(!empty($folderid))?"frid:".$folderid.",":""?>
          <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
         },
         dataType: "json",
         success: function(outEvoVal) {    
           // console.log(outEvoVal);
           $("#spinner").fadeOut("slow"); 
           Morris.Bar({  
            element: 'morris-area-chart2',
            data: outEvoVal,
            xkey: 'x',
            ykeys: ['y'],
            labels: ['Коэффициент освоения темы'],
            lineColors: ['#0000FF','#044c29'],
            lineWidth: 2,
            parseTime:false,
           });    
        }    
       });
      }

      function diag3() {
        $('#morris-area-chart3').html('');
        $("#spinner").fadeIn("slow"); 
        $.ajax({    
         url: 'diag3.json',
         type: 'POST',
         data: { 
          <?=(!empty($testid))?"tid:'".$testid."',":""?>
          <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
          <?=(!empty($edate))?"edate:'".$edate."',":""?>
          <?=(!empty($userid))?"uid:".$userid.",":""?>
          <?=(!empty($groupid))?"grid:".$groupid.",":""?>
          <?=(!empty($folderid))?"frid:".$folderid.",":""?>
          <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
         },
         dataType: "json",
         success: function(outEvoVal) {    
           //console.log(outEvoVal);
           $("#spinner").fadeOut("slow"); 
           Morris.Bar({  
            element: 'morris-area-chart3',
            data: outEvoVal,
            xkey: 'x',
            ykeys: ['y'],
            labels: ['Решаемость задания'],
            lineColors: ['#0000FF','#044c29'],
            lineWidth: 2,
            parseTime:false,
           });    
        }    
       });
      }

      function declOfNum(number, titles)  
      {  
        cases = [2, 0, 1, 1, 1, 2];  
        return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];  
      } 
 
      function diag4() {
        $('#morris-area-chart4').html('');
        $("#spinner").fadeIn("slow"); 
        $.ajax({    
         url: 'diag4.json',
         type: 'POST',
         data: { 
          <?=(!empty($testid))?"tid:'".$testid."',":""?>
          <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
          <?=(!empty($edate))?"edate:'".$edate."',":""?>
          <?=(!empty($userid))?"uid:".$userid.",":""?>
          <?=(!empty($groupid))?"grid:".$groupid.",":""?>
          <?=(!empty($folderid))?"frid:".$folderid.",":""?>
          <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
         },
         dataType: "json",
         success: function(outEvoVal) {    
//           console.log(outEvoVal);
           $("#spinner").fadeOut("slow"); 
           Morris.Donut({  
            element: 'morris-area-chart4',
            data: outEvoVal,
            formatter: function (y) { return y + " " + declOfNum(y, ["результат","результата","результатов"]) },
//            xkey: 'x',
//            ykeys: ['y'],
//            labels: ['Количество участников'],
//            lineColors: ['#0000FF','#044c29'],
//            lineWidth: 2,
            parseTime:false,
           });    
        }    
       });
      }
      
      function diag5() {
        $('#morris-area-chart5').html('');
        $("#spinner").fadeIn("slow"); 

        $.post('diag5.json',{
          <?=(!empty($testid))?"tid:'".$testid."',":""?>
          <?=(!empty($bdate))?"bdate:'".$bdate."',":""?>
          <?=(!empty($edate))?"edate:'".$edate."',":""?>
          <?=(!empty($userid))?"uid:".$userid.",":""?>
          <?=(!empty($groupid))?"grid:".$groupid.",":""?>
          <?=(!empty($folderid))?"frid:".$folderid.",":""?>
          <?=(!empty($folder_parent_id))?"frpid:".$folder_parent_id.",":""?>
          },
          function(data){  
            eval('var obj='+data);         
            if(obj.ok=='1')
            {
              $('#morris-area-chart5').html(obj.content);        
            } 
            $('#spinner').fadeOut("slow");
          });  
      }

      
      
<?}?>           
   </script>                        
  </body>
</html>
            