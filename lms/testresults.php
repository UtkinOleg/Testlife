<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

//  ini_set('display_errors', 1);
//  error_reporting(E_ALL); // E_ALL

  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;
  
  $testid = $_GET["tid"];
  $begindate = $_GET["bdate"];
  $enddate = $_GET["edate"];

  $begindate1 = $_GET["bdate"];
  $enddate1 = $_GET["edate"];
  
  if (!empty($begindate))
  {
   $DateTime1 = DateTime::createFromFormat('Y-m-d H:i:s', $begindate.' 00:00:00'); // Начало суток
   $begindate = $DateTime1->format('Y-m-d H:i:s');
  }
  if (!empty($enddate))
  {
   $DateTime1 = DateTime::createFromFormat('Y-m-d H:i:s', $enddate.' 23:59:59');  // Конец суток
   $enddate = $DateTime1->format('Y-m-d H:i:s');
  }
  
  $userid = $_GET["uid"];
  $xls = $_GET["xls"];

  $order = $_GET["order"];
  if (empty($order))
   $order1 = "ORDER BY s.id DESC";
  else
  {
   if ($order == "pnbasc")
    $order1 = "ORDER BY s.rightball ASC";
   else
   if ($order == "pnbdesc")
    $order1 = "ORDER BY s.rightball DESC";
   else
   if ($order == "dataasc")
    $order1 = "ORDER BY s.id ASC";
   else
   if ($order == "datadesc")
    $order1 = "ORDER BY s.id DESC";
  } 
  if (empty($xls)) 
   $xls = 0;
  

  $selector = "";
  if (!empty($testid))
    $selector = " WHERE s.testid='".$testid."'";
  if (!empty($begindate) and !empty($enddate))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
    else
     $selector = " WHERE s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
   } 
  if (!empty($userid))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.userid='".$userid."'";
    else
     $selector = " WHERE s.userid='".$userid."'";
   } 

  if(defined("IN_ADMIN"))
   $q = "SELECT s.* FROM singleresult as s".$selector." ".$order1; 
  else
  {
  if ($selector == "")
   $q = "SELECT s.* FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
  else
   $q = "SELECT s.* FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
  }

  if(defined("IN_ADMIN"))
   $q1 = "SELECT count(*) FROM singleresult as s".$selector; 
  else
  {
  if ($selector == "")
   $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID; 
  else
   $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID; 
  }

  if ($xls==0)
  {
  $tot = mysqli_query($mysqli,$q1);
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysqli_fetch_array($tot);
  $counter = $total['count(*)'];

  if ($counter==0)
   $title=$titlepage="Результаты тестирования";
  else
   $title=$titlepage="Результаты тестирования (".$counter.")";
  }
  
  if ($xls==1)
   $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q . ";");
  else
   $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q . " LIMIT ".$start.", ".$pnumber.";");
  if (!$res) puterror("Ошибка при обращении к базе данных");

  
if ($xls==1) {  
/** Include PHPExcel */
require_once dirname(__FILE__).'/lib/Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Экспертная система оценки проектов")
							 ->setLastModifiedBy("Экспертная система оценки проектов")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory($title);
} 
else 
{              

  // Выводим шапку страницы
  include "topadmin.php";

?>
<script src="lms/scripts/jquery.easing.min.js"></script>
<script src="lms/scripts/jquery.easypiechart.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('.fancybox').fancybox();

  $("#findresult").click(function() {
				$.fancybox.open({
					href : 'searchresult&tid=<?echo$testid?>&bdate=<?echo$begindate?>&edate=<?echo$enddate?>&uid=<?echo$userid?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 400,
          fitToView : true,
          autoSize : false,          
					padding : 5
				});
			});

  $("#report2").click(function() {
				$.fancybox.open({
					href : 'resreport&type=2',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 600,
          fitToView : true,
          autoSize : false,          
					padding : 5
				});
			});

  $("#report3").click(function() {
				$.fancybox.open({
					href : 'resreport&type=3',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 600,
          fitToView : true,
          autoSize : false,          
					padding : 5
				});
			});

  $("#report4").click(function() {
				$.fancybox.open({
					href : 'resreport&type=4',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 600,
          fitToView : true,
          autoSize : false,          
					padding : 5
				});
			});

/*  $(".fa").mouseover(function (e) {
      $(this).toggleClass('fa-spin');
  }).mouseout(function (e) {
      $(this).toggleClass('fa-spin');
  });  */
      
});
function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
 }    
function closeFancybox(){
    $.fancybox.close();
 }    
$(function() {
    $("#findresult").button();
    $("#report1").button();
    $("#report2").button();
    $("#report3").button();
    $("#report4").button();
    $("#data_asc").button();
    $("#data_desc").button();
    $("#pnb_asc").button();
    $("#pnb_desc").button();
    $("button").button();
});  
 
</script>

  <?
}


  if ($xls==1) {  
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '№')
            ->setCellValue('B1', 'Имя (Email)');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);        
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C1', 'Тест')
            ->setCellValue('D1', 'Всего вопросов');  
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);        
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E1', 'Правильно отвечено')
            ->setCellValue('F1', 'Баллов получено');  
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('G1', 'П.Н.Б. (%)')
            ->setCellValue('H1', 'Дата и время');  
  }
  else
  {
  
  echo "<p align='center'>
     <a style='font-size:1em;' id='findresult' href='javascript:;'><i class='fa fa-search fa-lg'></i>&nbsp;Поиск результатов</a>&nbsp;
     ";
  echo "<a style='font-size:1em;' id='report1' href='viewtestresults&tid=".$testid."&uid=".$userid."&bdate=".$begindate1."&edate=".$enddate1."&xls=1'><i class='fa fa-file-excel-o fa-lg'></i>&nbsp;Итоговая ведомость</a>";
  echo "</p>";

  // Выводим ссылки на предыдущие и следующие 
  echo "<p align=center>";      
  $i=1;
  $start2 = 0;
  if ($counter>$pnumber)
  while ($counter > 0)
  {
    if ($start==$start2)
     echo $i."&nbsp;";
    else
    {
     ?>
     <button title="Страница №<? echo $i;?>" onclick="document.location='viewtestresults&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=<? echo $order?>&start=<? echo $start2; ?>'"><? echo $i;?></button>&nbsp;
     <?
    }
    $i++;
    $counter = $counter - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";
  mysqli_free_result($tot);

/*  if (!empty($testid))
  {
   echo "<p align='center'><a style='font-size:1em;' id='report2' href='javascript:;'><i class='fa fa-bar-chart fa-lg'></i>&nbsp;Плотность распределения баллов</a>";
   echo "&nbsp;<a style='font-size:1em;' id='report3' href='javascript:;'><i class='fa fa-bar-chart fa-lg'></i>&nbsp;Коэффициент решаемости заданий</a>";
   echo "&nbsp;<a style='font-size:1em;' id='report4' href='javascript:;'><i class='fa fa-bar-chart fa-lg'></i>&nbsp;Коэффициент освоения групп вопросов</a>";
   echo "</p>";
  }  */
 
//  printf("MYSQLND_QC_ENABLE_SWITCH: %s\n", MYSQLND_QC_ENABLE_SWITCH);
//  echo "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q;
  
  $tableheader = "class=tableheaderhide";
    ?>
    <p align='center'>
     <div id='menu_glide' class='menu_glide'>
      <table align='center' width='100%' style="background-color: #FFFFFF;" class='bodytable' border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td align='center' witdh='50'><p class=help>№</p></td>
              <td witdh='500'><p class=help>Имя (Email)</p></td>
              <td align='center' witdh='50'><p><i class='fa fa-user fa-lg' style="color:#fff;"></i></p></td>
              <td witdh='400'><p class=help>Тест</p></td>
              <td align='center' witdh='100'><p class=help>Всего вопросов</p></td>
              <td align='center' witdh='100'><p class=help>Правильно отвечено</p></td>
              <td align='center' witdh='100'><p class=help>Баллов получено <a id="pnb_asc" href="javascript:;" onclick="location.replace('viewtestresults&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=pnbasc');">
              <i class='fa fa-sort-asc'></i></a> <a id="pnb_desc" href="javascript:;"  onclick="location.replace('viewtestresults&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=pnbdesc');"><i class='fa fa-sort-desc'></i></a>
              </p></td>
              <td align='center' witdh='100'><p class=help>П.Н.Б.(%)</p></td>
              <td align='center' witdh='100'><p class=help>Дата и время <a id="data_asc" href="javascript:;"  onclick="location.replace('viewtestresults&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=dataasc');"><i class='fa fa-sort-asc'></i></a>
              <a id="data_desc" href="javascript:;"  onclick="location.replace('viewtestresults&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=datadesc');"><i class='fa fa-sort-desc'></i></a></p></td>
              <td align='center' witdh='10'><p><i style="color:#fff;" class='fa fa-question-circle fa-lg'></i></p></td>
              <td align='center' witdh='10'><p><i style="color:#fff;" class='fa fa-trash fa-lg'></i></p></td>
          </tr>   
     <?         
 }
  $i=$start;
  while($member = mysqli_fetch_array($res))
  {
 if ($xls==0)
 {
    ?>
<style type="text/css">
.chart<? echo $member['id']?> {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 60px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
}
.chart<? echo $member['id']?> canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent<? echo $member['id']?> {
  display: inline-block;
  line-height: 60px;
  z-index: 2;
  font-size: .8em;
}
.percent<? echo $member['id']?>:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
<script type="text/javascript">
  $(function() {
    $('.chart<? echo $member['id']?>').easyPieChart({
      size : 60,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent<? echo $member['id']?>').text(Math.round(percent));
			}		
    });
  });
  $(document).ready(function() {
    	$("#viewresult<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'testresults&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
  });
</script>
    <?
 }
 $i++;
 if ($xls==1)
 {
    $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio, email FROM users WHERE id='".$member['userid']."' LIMIT 1;");
    $fromuser = mysqli_fetch_array($from);
    $ii=$i+1;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$ii, ''.$i)
            ->setCellValue('B'.$ii, ''.$fromuser['userfio'].'('.$fromuser['email'].')');
    mysqli_free_result($from);

    $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
    $testname = mysqli_fetch_array($test);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C'.$ii, ''.$testname['name'])
            ->setCellValue('D'.$ii, ''.$member['allq']);  
    mysqli_free_result($test);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E'.$ii, ''.$member['rightq'])
            ->setCellValue('F'.$ii, ''.$member['rightball'].' из '.$member['allball']);  
    $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('G'.$ii, ''.$percent)
            ->setCellValue('H'.$ii, ''.data_convert ($member['resdate'], 1, 1, 0));  
 }
 else
 {
    echo "<tr><td align='center' witdh='50'><p>".$i."</p></td>";

    $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio, email FROM users WHERE id='".$member['userid']."' LIMIT 1;");
    $fromuser = mysqli_fetch_array($from);
    if(defined("IN_ADMIN"))
     echo "<td><p><a target='_blank' href='edituser&id=".$member['userid']."'>".$fromuser['userfio']." (".$fromuser['email'].")</a></p></td>";
    else
     echo "<td><p><a target='_blank' href='viewuser&id=".$member['userid']."'>".$fromuser['userfio']." (".$fromuser['email'].")</a></p></td>";
    mysqli_free_result($from);

    $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
    $testname = mysqli_fetch_array($test);
    echo "<td align='center'>";
    if ($testname['testtype']=='pass') { 
     echo "<i class='fa fa-check-square-o fa-lg' title='Зачетный тест'></i> "; 
    } else 
    if ($testname['testtype']=='check') {
     echo "<i class='fa fa-share-square-o fa-lg' title='Проверочный тест'></i> "; 
    }
    echo "</td><td><p>".$testname['name']."</p></td>";
    mysqli_free_result($test);

    echo "<td align='center' ><p>".$member['allq']."</p></td>";
    echo "<td align='center' ><p>".$member['rightq']."</p></td>";

    echo "<td align='center' ><p>".$member['rightball']." из ".$member['allball']."</p></td>";
    $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    echo "<td align='center'>
    <div class='chart".$member['id']."' data-percent='".$percent."' data-scale-color='#ffb400'><span class='percent".$member['id']."'></span></div></td>";
    echo "<td align='center'><p>".data_convert ($member['resdate'], 1, 1, 0)."</p></td>";

?>
<td align='center'><a title="Результаты и протокол тестирования" id="viewresult<? echo $member['id']; ?>" href="javascript:;"><i class='fa fa-question-circle fa-lg'></i></a>
</td><td><a href="#" onClick="DelWindow(<? echo $member['id'];?> ,<? echo $start;?>,'deltestresult&tid=<? echo $testid;?>&bdate=<? echo $begindate1?>&edate=<? echo $enddate1?>&uid=<? echo $userid?>&order=<? echo $order?>','viewtestresults','результат')" title="Удалить результат"><i class='fa fa-trash fa-lg'></i></a>
</td></tr>    
<?
  }
 }
  
  mysqli_free_result($res);

  if ($xls==1)
  {

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('TestResults');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="testresult.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

  }
  else
  {
   echo "</table></div></p>";

  include "bottomadmin.php";
  }
} else die;  
  
?>