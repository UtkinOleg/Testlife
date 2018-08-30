<?
if(!defined("USER_REGISTERED")) die;  

require_once "showquestion.php";

spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
});

function puterror($message)
{
    echo("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body><p align='center'>$message</p></body></html>");
    exit();
}

function data_convert($data, $year, $time, $second) {
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
}

function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
function str2url($str) {
    $str = rus2translit($str);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = trim($str, "-");
    return $str;
}

include "config.php";

//ini_set('display_errors', 1);
//error_reporting(E_ALL); // E_ALL

$sign = $_GET['sign']; // Сигнатура ответов
$sid = $_GET['tid']; // Сигнатура Теста
$resultid = $_GET['id']; // ID Результата
$reloadurl = $_GET['url'];

$rtf = $_GET['rtf']; // Печать
if (empty($rtf)) $rtf = 0;

$log=0;

if ($rtf == 0)
{
?>
<!DOCTYPE html>
<html lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lms/css/custom-theme/jquery-ui-1.10.3.custom.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="lms/scripts/jquery-ui/jquery-ui.min.js"></script>
    <script src="lms/scripts/jquery.easing.min.js"></script>
    <script src="lms/scripts/jquery.easypiechart.min.js"></script>
    <script>
    $(function() {
     $( "#viewprotocol" ).button();
     $( "#close" ).button();
     $( "#print" ).button();
     $('.chart').easyPieChart({
      size : 100,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}		
     });
     $('.charti').easyPieChart({
      size : 100,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percenti').text(Math.round(percent));
			}		
     });
    });
    </script>
<?
}

if (empty($resultid))
{
 $query = "SELECT * FROM testgroups WHERE signature='".$sid."' LIMIT 1;";
 $tst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($tst) 
  $test = mysqli_fetch_array($tst);
 else 
  puterror("Ошибка при обращении к базе данных");
 $testname = $test['name'];
 $userfio = USER_FIO;
 $userfiocl = USER_FIO;
 $tid = $test['id'];
 $psytest = $test['psy'];
 $scaleid = $test['scale'];

 if ($psytest)
  $psitest = new Psytest($mysqli, $tid, '', USER_ID);
 mysqli_free_result($tst); 
}
else
{
 $query = "SELECT * FROM singleresult WHERE signature='".$resultid."' LIMIT 1;";
 $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($res) 
  $result = mysqli_fetch_array($res);
 else 
  puterror("Ошибка при обращении к базе данных");
 $tid = $result['testid'];
 $resultid = $result['id'];
 $userid = $result['userid'];
 $sign = $result['signature'];
 $itog_rightsumma = $result['rightball'];
 $itog_nonsumma = $result['allball'];
 $itog_sumball = $result['rightq'];
 $itog_allq = $result['allq'];
 $testdate = data_convert ($result['resdate'], 1, 1, 0);
 mysql_free_result($res); 

 $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio, photoname, token FROM users WHERE id='".$userid."' LIMIT 1;");
 $fromuser = mysqli_fetch_array($from);
 $userfio = $fromuser['userfio'];
 $userfiocl = $fromuser['userfio'];
 mysqli_free_result($from);
 
 $query = "SELECT * FROM testgroups WHERE id=".$tid." LIMIT 1;";
 $tst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($tst) 
  $test = mysqli_fetch_array($tst);
 else 
  puterror("Ошибка при обращении к базе данных");
 $testname = $test['name'];
 $psytest = $test['psy'];
 $scaleid = $test['scale'];

 mysqli_free_result($tst); 
 $printbtn = '<a class="ui-button-primary" href="testresults&rtf=1&id='.$sign.'" style="font-size: 1em;" id="print"><i class="fa fa-print fa-lg"></i> Печать</a>&nbsp;';

}      

if ($rtf == 0)
{
if (!empty($resultid)) {?>
<link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="lms/scripts/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
<?}?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.9em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 0px; bottom : 50px;  position: absolute; overflow: auto;} 
p {
  font: 16px / 1.4 'Helvetica', 'Arial', sans-serif;
}
.chart {
  position: relative;
  display: inline-block;
  width: 100px;
  height: 100px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
  font: 18px / 1.4 'Helvetica', 'Arial', sans-serif;
}
.chart canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent {
  display: inline-block;
  line-height: 100px;
  z-index: 2;
}
.percent:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
.charti {
  position: relative;
  display: inline-block;
  width: 100px;
  height: 100px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
  font: 18px / 1.4 'Helvetica', 'Arial', sans-serif;
}
.charti canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percenti {
  display: inline-block;
  line-height: 100px;
  z-index: 2;
}
.percenti:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
</head>
<body>
    <div id="buttonsetm">
     <div class="panel panel-primary">
                        <div class="panel-heading">
                            <strong><?=$testname?></strong>&nbsp;<span class='pull-right'><?=$userfio?><? if (!empty($testdate)) echo "&nbsp;&middot;&nbsp;".$testdate; ?></span>
                        </div>
                        <div class="panel-body">
     <div class='table-responsive'>
      <table class='table' width='98%'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='left' witdh='400'>Группа вопросов (раздел, тема)</td>
              <? if ($psytest) {?>
              <td>Результат</td>
              <?} else {?>
              <td align='center' witdh='50'>Вопросы</td>
              <td align='center' witdh='50'>Правильно</td>
              <td align='center' witdh='50'>Неправильно</td>
              <td align='center' witdh='50'>Баллы</td>
              <td align='center' witdh='50'>Уровень освоения</td>
              <?}?>
          </thead> 
          <tbody>  
<?         
}
else
{

$dir = dirname(__FILE__);
require_once $dir . '/lib/PHPRtfLite.php';

$rowCount = 1;
$rowHeight = 1;
$columnCount = 7;
$columnWidth = 3;

PHPRtfLite::registerAutoloader();

$rtfs = new PHPRtfLite();


$sect = $rtfs->addSection();

$sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
//$sect->addImage($dir . '/img/logoexpert.png', null );
$sect->writeText('<i>Тест: <b>'.$testname.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText('<i>Имя: <b>'.$userfiocl.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText('<i>Дата: <b>'.$testdate.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));

$table = $sect->addTable();
$table->addRows(1);
if ($psytest)
 $table->addColumnsList(array(1,7,7));
else
 $table->addColumnsList(array(1,4,2,2,2,2,2));

$cell = $table->getCell(1,1);
$cell->writeText("№");
$cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
$cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

$cell = $table->getCell(1,2);
$cell->writeText("Группа (раздел, тема)");
$cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
$cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

if ($psytest)
{
 $cell = $table->getCell(1,3);
 $cell->writeText("Результат");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
}
else
{
 $cell = $table->getCell(1,3);
 $cell->writeText("Вопросы");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,4);
 $cell->writeText("Правильно");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,5);
 $cell->writeText("Неправильно");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,6);
 $cell->writeText("Баллы");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,7);
 $cell->writeText("Уровень освоения");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
}

$borderTop = new PHPRtfLite_Border($rtfs);
$borderTop->setBorderTop(new PHPRtfLite_Border_Format(1, '#000'));
$table->setBorderForCellRange($borderTop, 1, 1, 1, $columnCount);

$borderBottom = new PHPRtfLite_Border($rtfs);
$borderBottom->setBorderBottom(new PHPRtfLite_Border_Format(1, '#000'));
$table->setBorderForCellRange($borderBottom, $rowCount, 1, $rowCount, $columnCount);

}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$showprotocol = true;

if (empty($resultid))  // Процесс сканирования результатов и записи
{

      $showprotocol = false;

      $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmptest WHERE signature='".$sign."' ORDER BY qgroupid DESC;");
      $ctd = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmptest WHERE signature='".$sign."' LIMIT 1;");
      if (!$td) puterror("Ошибка при обращении к базе данных");
      if (!$ctd) puterror("Ошибка при обращении к базе данных");
      $cnttd = mysqli_fetch_array($ctd);
      $qc=0;
      $aqc=0;
      $tt=0;
      $sumball=0;  // Количество правильных ответов по группе
      $rightsumma=0; // Сумма правильно набранных баллов по группе
      $nonsumma=0; // Сумма всех баллов по группе
      $itog_sumball=0;  // Количество правильных ответов по тесту
      $itog_rightsumma=0; // Сумма правильно набранных баллов по тесту
      $itog_nonsumma=0; // Сумма неправильно набранных баллов по тесту
      $i=0;                      
      $userid = USER_ID;
      
      while($testdata = mysqli_fetch_array($td))
      {
       if ($i==0)
       {
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1");
        $questgroup = mysqli_fetch_array($qg);
        $qc++;
        echo "<tr><td align='center'><p>".++$i."</p></td><td><p>".$questgroup['name']."</p>";
        if (!empty($questgroup['comment'])) echo "<p style='font-size:0.7em;'>".$questgroup['comment']."</p>";
        echo"</td>";
        $lastid = $testdata['qgroupid'];
        mysqli_free_result($qg);
       }
       else
        $qc++;
        
       $aqc++; 

       if ($lastid != $testdata['qgroupid'])      // Новая группа !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
       {
        $qc--;
        $nball = $qc-$sumball;
        
        ?>
<style type="text/css">
.chart<? echo $i?> {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 60px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
}
.chart<? echo $i?> canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent<? echo $i?> {
  display: inline-block;
  line-height: 60px;
  z-index: 2;
  font-size: .8em;
}
.percent<? echo $i?>:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
<script>
  $(function() {
    $('.chart<? echo $i?>').easyPieChart({
      size : 60,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent<? echo $i?>').text(Math.round(percent));
			}		
    });
  });
</script>
        <?
        $psy = '';
        if ($psytest)
        {
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
          $psy .= "<p>".$key." <span class='badge'>".$val."</span></p>";
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        }
        else
        {
         echo "<td align='center'><p>".$qc."</p></td><td align='center'><p>".$sumball."</p></td>
         <td align='center'><p>".$nball."</p></td><td align='center'><p>".$rightsumma." из ".$nonsumma."</p></td>";
         $percent = (int) floor($rightsumma / $nonsumma * 100);
         echo "<td align='center'>
         <div class='chart".$i."' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percent".$i."'></span></div>
         </td></tr>";
        }
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
        
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Проверка на существование результата 
        if ($log>0) echo " Группа записи = ".$lastid."<br>";
        $tmpres = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testresults WHERE testid='".$tid."' AND qgroupid='".$lastid."' AND signature='".$sign."' ORDER BY id;");
        $totalres = mysqli_fetch_array($tmpres);
        $allrescount = $totalres['count(*)'];
        if ($allrescount==0)
        {
         if ($log>0) echo " Группа ".$lastid." записана.<br>";
         mysqli_query($mysqli,"START TRANSACTION;");
         $userid = USER_ID;
         mysqli_query($mysqli,"INSERT INTO testresults VALUES (0,
                                        $tid,
                                        $lastid,
                                        $userid,
                                        $qc,
                                        $sumball,
                                        $nonsumma,
                                        $rightsumma,
                                        '$sign',
                                        NOW(),
                                        '$psy');");
         mysqli_query($mysqli,"COMMIT");
        }
        mysqli_free_result($tmpres);
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        $sumball = 0;
        $rightsumma = 0;
        $nonsumma = 0;
        $qc=1;
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1");
        $questgroup = mysqli_fetch_array($qg);
        echo "<tr><td align='center'><p>".++$i."</p></td><td><p>".$questgroup['name']."</p></td>";
        $lastid = $testdata['qgroupid'];
        mysqli_free_result($qg);

       if ($aqc==$cnttd['count(*)'])
       {
        
        ?>
<style type="text/css">
.chart<? echo $i?> {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 60px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
}
.chart<? echo $i?> canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent<? echo $i?> {
  display: inline-block;
  line-height: 60px;
  z-index: 2;
  font-size: .8em;
}
.percent<? echo $i?>:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
<script>
  $(function() {
    $('.chart<? echo $i?>').easyPieChart({
      size : 60,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent<? echo $i?>').text(Math.round(percent));
			}		
    });
  });
</script>
        <?
        
       // Найдем балл за правильный ответ на последний вопрос 
       // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
       $rtball=1; // По умолчанию - 1
       $qgp = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1;");
       if ($qgp)
       {
         $questgp = mysqli_fetch_array($qgp);
         $rtball = $questgp['singleball'];
       } 
       else if ($log>0) echo "err ";
       mysqli_free_result($qgp); 
       
       // Просканируем ответы для психологического теста 
       if ($psytest)
       {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
         if ($userans)
          while($useranswer = mysqli_fetch_array($userans))
           $psitest->setPsyAnswer($testdata['questionid'], $useranswer['answerid'], $rtball);
         mysqli_free_result($userans); 
       }
       else
       // Правильный 
       if (IsRightAnswer($mysqli, $testdata['questionid'], $sign, ''))
       {
        $sumball++;
        $rightsumma += $rtball;
       }
       $nonsumma += $rtball;
       // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $nball = $qc-$sumball;
        
        $psy = '';
        if ($psytest)
        {
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
          $psy .= "<p>".$key." <span class='badge'>".$val."</span></p>";
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        }
        else
        {
         echo "<td align='center'><p>".$qc."</p></td><td align='center'><p>".$sumball."</p></td>
         <td align='center'><p>".$nball."</p></td><td align='center'><p>".$rightsumma." из ".$nonsumma."</p></td>";
         $percent = (int) floor($rightsumma / $nonsumma * 100);
         echo "<td align='center'>
         <div class='chart".$i."' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percent".$i."'></span></div>
         </td></tr>";
        }
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
        
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Проверка на существование результата 
        if ($log>0) echo " Группа записи = ".$lastid."<br>";
        $tmpres = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testresults WHERE testid='".$tid."' AND qgroupid='".$lastid."' AND signature='".$sign."' ORDER BY id;");
        $totalres = mysqli_fetch_array($tmpres);
        $allrescount = $totalres['count(*)'];
        if ($allrescount==0)
        {
         if ($log>0) echo " Группа ".$lastid." записана.<br>";
         mysqli_query($mysqli,"START TRANSACTION;");
         $userid = USER_ID;
         mysqli_query($mysqli,"INSERT INTO testresults VALUES (0,
                                        $tid,
                                        $lastid,
                                        $userid,
                                        $qc,
                                        $sumball,
                                        $nonsumma,
                                        $rightsumma,
                                        '$sign',
                                        NOW(),
                                        '$psy');");
         mysqli_query($mysqli,"COMMIT");
        }
        mysqli_free_result($tmpres);
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        break;
       }

        
       }
       else
       if ($aqc==$cnttd['count(*)'])
       {
        
        ?>
<style type="text/css">
.chart<? echo $i?> {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 60px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
}
.chart<? echo $i?> canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent<? echo $i?> {
  display: inline-block;
  line-height: 60px;
  z-index: 2;
  font-size: .8em;
}
.percent<? echo $i?>:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
<script>
  $(function() {
    $('.chart<? echo $i?>').easyPieChart({
      size : 60,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent<? echo $i?>').text(Math.round(percent));
			}		
    });
  });
</script>
        <?
        
        // Найдем балл за правильный ответ на последний вопрос 
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $rtball=1; // По умолчанию - 1
        $qgp = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1;");
        if ($qgp)
        { 
         $questgp = mysqli_fetch_array($qgp);
         $rtball = $questgp['singleball'];
        } 
        else if ($log>0) echo "err ";
        mysqli_free_result($qgp); 
       

        // Просканируем ответы для психологического теста 
        if ($psytest)
        {
          $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
          if ($userans)
          while($useranswer = mysqli_fetch_array($userans))
           $psitest->setPsyAnswer($testdata['questionid'], $useranswer['answerid'], $rtball);
          mysqli_free_result($userans); 
        }
        else
        // Правильный 
        if (IsRightAnswer($mysqli, $testdata['questionid'], $sign, ''))
        {
         $sumball++;
         $rightsumma += $rtball;
        }
        $nonsumma += $rtball;
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $nball = $qc-$sumball;
        $psy = '';
        if ($psytest)
        {
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
          $psy .= "<p>".$key." <span class='badge'>".$val."</span></p>";
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        }
        else
        {
         echo "<td align='center'><p>".$qc."</p></td><td align='center'><p>".$sumball."</p></td>
         <td align='center'><p>".$nball."</p></td><td align='center'><p>".$rightsumma." из ".$nonsumma."</p></td>";
         $percent = (int) floor($rightsumma / $nonsumma * 100);
         echo "<td align='center'>
         <div class='chart".$i."' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percent".$i."'></span></div>
         </td></tr>";
        }
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
        
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Проверка на существование результата 
        if ($log>0) echo " Группа записи = ".$lastid."<br>";
        $tmpres = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testresults WHERE testid='".$tid."' AND qgroupid='".$lastid."' AND signature='".$sign."' ORDER BY id;");
        $totalres = mysqli_fetch_array($tmpres);
        $allrescount = $totalres['count(*)'];
        if ($allrescount==0)
        {
         if ($log>0) echo " Группа ".$lastid." записана.<br>";
         mysqli_query($mysqli,"START TRANSACTION;");
         $userid = USER_ID;
         mysqli_query($mysqli,"INSERT INTO testresults VALUES (0,
                                        $tid,
                                        $lastid,
                                        $userid,
                                        $qc,
                                        $sumball,
                                        $nonsumma,
                                        $rightsumma,
                                        '$sign',
                                        NOW(),
                                        '$psy');");
         mysqli_query($mysqli,"COMMIT");
        }
        mysqli_free_result($tmpres);
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        
        break;
       }
       
       // Найдем балл за правильный ответ
       // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
       $rtball=1; // По умолчанию - 1
       $qgp = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1;");
       if ($qgp)
       {
         $questgp = mysqli_fetch_array($qgp);
         $rtball = $questgp['singleball'];
       } 
       else if ($log>0) echo "err ";
       mysqli_free_result($qgp); 

       // Просканируем ответы для психологического теста 
       if ($psytest)
       {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
         if ($userans)
          while($useranswer = mysqli_fetch_array($userans))
           $psitest->setPsyAnswer($testdata['questionid'], $useranswer['answerid'], $rtball);
         mysqli_free_result($userans); 
       }
       else
       // Правильный 
       if (IsRightAnswer($mysqli, $testdata['questionid'], $sign, ''))
       {
        $sumball++;
        $rightsumma += $rtball;
       }
       $nonsumma += $rtball;
       // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
       
      }
      $itog_allq = $cnttd['count(*)'];
      mysqli_free_result($ctd);
      mysqli_free_result($td);
      
      echo "</tbody></table></div>";
      
        // Запишем итоговый результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Проверка на существование итогов результата 
        $tmpres = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult WHERE testid='".$tid."' AND signature='".$sign."' ORDER BY id;");
        $totalres = mysqli_fetch_array($tmpres);
        $allrescount = $totalres['count(*)'];
        if ($allrescount==0)
        {
         
      //    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT testkind FROM testgroups WHERE id = $tid LIMIT 1;");
      /*    $adapt = mysqli_fetch_array($sql);
          $testkind = $adapt['testkind'];
          mysqli_free_result($sql);
          if ($testkind=='adaptive')
          {
           $test = new Test($mysqli, $tid, $sign, USER_ID);
           if (!empty($test))
           {

      $maxball = 0;

      $minimum = 1000000; 
      $maximum = 0;
      $minimumball = 0; 
      $maximumball = 0;
      $minimumG = $test->getGroup($test->getMinId());
      $maximumG = $test->getGroup($test->getMaxId());
      
      foreach($test->getGroups() as $group) 
      {
        if ($group->getDifficultys() < $minimum)
        {
         $minimum = $group->getDifficultys();
         $minimumball = $group->getBall();
         $minimumG = $group;
        }
        
        if ($group->getDifficultys() > $maximum)
        {
         $maximum = $group->getDifficultys();
         $maximumball = $group->getBall();
         $maximumG = $group;
        }
      }
      
      if ($maximum == $minimum)
       $maxball = $maximumball * 7;
      else
      {
        $aver = (int)floor(($maximum + $minimum) / 2);
        $averId = 0;
        foreach($test->getGroups() as $group) 
        {
         if ($group->getDifficultys() === $aver)
         {
          $averId = $group->getId();
          break;
         }
        }
        
        if ($averId>0)
        {
          $avergroup = $test->getGroup($averId);
          $maxball = $avergroup->getBall()*2;
          while ($test->GetHighLevelGroup($avergroup) != $maximumG)
          {
           $avergroup = $test->GetHighLevelGroup($avergroup);
           $maxball += $avergroup->getBall() * 2;
          }
          $maxball += $maximumball * 7;
        }
        else // Группа не найдена - тогда неравномерное распределение сложности
        {
          if ($maximum == $minimum) // Две группы
          {
           $avergroup = $maximumG;
           $maxball = $maximumball * 2;

           while ($test->GetHighLevelGroup($avergroup) != $maximumG)
           {
            $avergroup = $test->GetHighLevelGroup($avergroup);
            $maxball += $avergroup->getBall() * 2;
           }
           
           $maxball += $maximumball * 7;
          
          }
          else // Больше двух ?
          {
           $avergroup = $test->getAverageGroup($minimumG, $maximumG);
           $maxball = $avergroup->getBall()*2;
           
           while ($test->GetHighLevelGroup($avergroup) != $maximumG)
           {
            $avergroup = $test->GetHighLevelGroup($avergroup);
            $maxball += $avergroup->getBall() * 2;
           }
           
           $maxball += $maximumball * 7;
          }
        }
      }           
            $itog_nonsumma = $maxball;
           }
          }   */ 
         
         mysqli_query($mysqli,"START TRANSACTION;");
         $userid = USER_ID;

         // Удалим попытку тестирования
         $tmpatt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM attemptsresult WHERE testid='".$tid."' AND userid='".$userid."' LIMIT 1;");
         if ($tmpatt != false)
         {
          $totalatt = mysqli_fetch_array($tmpatt);
          mysqli_query($mysqli,"DELETE FROM attemptsresult WHERE id=".$totalatt['id']);
          mysqli_free_result($tmpatt);
         }
         
         mysqli_query($mysqli,"INSERT INTO singleresult VALUES (0,
                                        $tid,
                                        $userid,
                                        $itog_allq,
                                        $itog_sumball,
                                        $itog_nonsumma,
                                        $itog_rightsumma,
                                        '$sign',
                                        NOW());");
         $resultid = mysqli_insert_id($mysqli);                               
         mysqli_query($mysqli,"COMMIT");
         $printbtn = '<a class="ui-button-primary" href="testresults&rtf=1&id='.$sign.'" style="font-size: 1em;" id="print"><i class="fa fa-print fa-lg"></i> Печать</a>&nbsp;';
        }
        mysqli_free_result($tmpres);
        
        
        // Запишем результат +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        
}
else  // Покажем записанные результаты
{
      $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testresults WHERE signature='".$sign."' AND testid='".$tid."' AND userid='".$userid."' ORDER BY id");
      $i=0;
      while($member = mysqli_fetch_array($td))
      {
        $i++;
        
if ($rtf==0) {        
?>
<style type="text/css">
.chart<? echo $i?> {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 60px;
  margin-top: 0px;
  margin-bottom: 0px;
  text-align: center;
}
.chart<? echo $i?> canvas {
  position: absolute;
  top: 0;
  left: 0;
}
.percent<? echo $i?> {
  display: inline-block;
  line-height: 60px;
  z-index: 2;
  font-size: .8em;
}
.percent<? echo $i?>:after {
  content: '%';
  margin-left: 0.1em;
  font-size: .8em;
}
</style>
<script>
  $(function() {
    $('.chart<? echo $i?>').easyPieChart({
      size : 60,
      easing: 'easeOutBounce',
      onStep: function(from, to, percent) {
				$(this.el).find('.percent<? echo $i?>').text(Math.round(percent));
			}		
    });
  });
</script>
<?  
}    
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$member['qgroupid']."' LIMIT 1");
        $questgroup = mysqli_fetch_array($qg);
        
        if ($rtf==0)
         echo "<tr><td align='center'><p>".$i."</p></td><td><p>".$questgroup['name']."</p>";
        else
        {
        
         $table->addRow(1);
         $cell = $table->getCell($i+1, 1);
         $cell->writeText($i);
         $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
         $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

         $cell = $table->getCell($i+1, 2);
         $cell->writeText($questgroup['name']);
         $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
         $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

        }
        if (!empty($questgroup['comment'])) 
         if ($rtf==0)
          echo "<p style='font-size:0.7em;'>".$questgroup['comment']."</p>";
                if ($rtf==0)
         echo"</td>";
        
        //$lastid = $testdata['qgroupid'];
        mysqli_free_result($qg);
        
        $nball = $member['allq'] - $member['rightq'];
        if ($rtf==0)
        {
         if ($psytest)
          echo "<td>".$member['psy']."</td></tr>";
         else
         {
         echo "<td align='center'><p>".$member['allq']."</p></td><td align='center'><p>".$member['rightq']."</p></td>
         <td align='center'><p>".$nball."</p></td><td align='center'><p>".$member['rightball']." из ".$member['allball']."</p></td>";
         }
        }
        else
        {
         if ($psytest)
         {
          $cell = $table->getCell($i+1, 3);
          $cell->writeText(strip_tags($member['psy']));
          $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
          $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
         } 
         else
         {
          $cell = $table->getCell($i+1, 3);
          $cell->writeText($member['allq']);
          $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
          $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

          $cell = $table->getCell($i+1, 4);
          $cell->writeText($member['rightq']);
          $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
          $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

          $cell = $table->getCell($i+1, 5);
          $cell->writeText($nball);
          $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
          $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

          $cell = $table->getCell($i+1, 6);
          $cell->writeText($member['rightball']." из ".$member['allball']);
          $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
          $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
         }
        }
        
        $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
         if ($rtf==0)
         {
          if (!$psytest)
           echo "<td align='center'>
           <div class='chart".$i."' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percent".$i."'></span></div>
           </td></tr>";
         }
         else
         {
          if (!$psytest)
          { 
           $cell = $table->getCell($i+1, 7);
           $cell->writeText($percent."%");
           $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
           $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
          }
         }
      }
      mysqli_free_result($td);

      if ($rtf==0)
       echo "</tbody></table></div>";
      
}
      
if ($rtf==0)
{

if (!$psytest)
{
?>    

<div class='table-responsive'>
  <table class='table' width='95%'>
   <tbody>
    <tr align='center'>
     <td align='center'>
     <div class='table-responsive'>
      <table class='table' width='280'>
      <tr><td align='center'>
<?

    $percent = (int) floor($itog_rightsumma / $itog_nonsumma * 100);

    if ($scaleid==0)
    {
    if ($percent<45) $ocenka = '<span class="badge" style="background-color:#F20909;">2</span>';
    else
    if ($percent>=45 and $percent<=69) $ocenka = '<span class="badge" style="background-color:#ED9C09;">3</span>';
    else
    if ($percent>=70 and $percent<=85) $ocenka = '<span class="badge" style="background-color:#153FE4;">4</span>';
    else
     $ocenka = '<span class="badge" style="background-color:#29B004;">5</span>';
    }
    else
    {
     $sqlsc2 = mysqli_query($mysqli,"SELECT * FROM scaleparams WHERE scaleid='".$scaleid."' ORDER BY id;");
     while($scpar = mysqli_fetch_array($sqlsc2))
     {
      $scpar_name = $scpar['name'];
      $scpar_top = $scpar['top'];
      $scpar_end = $scpar['end'];
      if ($percent>=$scpar_top and $percent<=$scpar_end)
       $ocenka = '<span class="badge">'.$scpar_name.'</span>';
     }    
     mysqli_free_result($sqlsc2);
    } 

    echo "<p align='center'>Итоговый балл: ".$itog_rightsumma." из ".$itog_nonsumma."</p><p>Оценка ".$ocenka."</p>";
    echo "</p>";
    echo "<div class='chart' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percent'></span></div>";

?>    
      </td></tr>
      </table>
      </div>
     </td>  
     <td align='center'>
     <div class='table-responsive'>
      <table class='table' width='280'>
      <tr><td align='center'>
<?
      $percent = (int) floor($itog_sumball / $itog_allq * 100);
      echo "<p align='center'>Правильных ответов: ".$itog_sumball." из ".$itog_allq."</p>";
      echo "</p>";
      echo "<div class='charti' data-percent='".$percent."' data-bar-color='#838EFA' data-scale-color='#838EFA'><span class='percenti'></span></div>";
?>    
      </td></tr>
      </table>
      </div>
     </td>  
    </tr> 
</tbody>
</table>
</div>    

<?}?>
   </div>
  </div>
 </div>
 <div id="buttonset"> 
    <? echo $printbtn;
       if ($showprotocol) {
        if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
          <button class="ui-button-primary" style="font-size: 1em;" id="viewprotocol"><i class='fa fa-search fa-lg'></i> Протокол тестирования</button>&nbsp; 
    <?} }?>
    <button class="ui-button-primary" style="font-size: 1em;" id="close" onclick="parent.closeFancybox();"><i class='fa fa-check fa-lg'></i> Закрыть</button> 
 </div>
 
<?if ($showprotocol){?>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>
    <script>
     function closeFancybox(){
      $.fancybox.close();
     }
     $(document).ready(function() {
    	$("#viewprotocol").click(function() {
				$.fancybox.open({
					href : 'resultprotocol&id=<? echo $resultid ?>',
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
<?}?>
</body>
</html>
<?
}
else
{
 if (!$psytest)
 { 
 $percent = (int) floor($itog_rightsumma / $itog_nonsumma * 100);
 $sect->writeText('<i>Итоговый балл: <b>'.$itog_rightsumma.' из '.$itog_nonsumma.'</i> ('.$percent.'%) </b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
 $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
 $percent = (int) floor($itog_sumball / $itog_allq * 100);
 $sect->writeText('<i>Правильно отвечено: <b>'.$itog_sumball.' из '.$itog_allq.'</i> ('.$percent.'%) </b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
 $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
 }

header('Content-Type: application/octet-stream');
$filename = str2url("result_".$testname."_1").".rtf";
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
header('Content-Transfer-Encoding: binary');

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

// save rtf document
$rtfs->save('php://output');
}
?>