<?
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

$sign = $_GET['sign']; // Сигнатура ответов
$sid = $_GET['tid']; // Сигнатура Теста
$resultid = $_GET['id']; // ID Результата
$reloadurl = $_GET['url'];
$rtf = 0;

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
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script>
    $(function() {
     $( "#close" ).button();
    });
    </script>
<?
}

 $query = "SELECT * FROM testgroups WHERE signature='".$sid."' LIMIT 1;";
 $tst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($tst) 
  $test = mysqli_fetch_array($tst);
 else 
  puterror("Ошибка при обращении к базе данных");
 $testname = $test['name'];
 $userfio = '';
 $userfiocl = '';
 $tid = $test['id'];
 $psytest = $test['psy'];
 if ($psytest)
  $psitest = new Psytest($mysqli, $tid, '', time());
 mysqli_free_result($tst); 

?>

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
              <td>Результат психологического тестирования</td>
          </thead> 
          <tbody>  
<?         

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Процесс сканирования результатов 

      $showprotocol = false;

      $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmptestcheck WHERE signature='".$sign."' ORDER BY qgroupid DESC;");
      $ctd = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmptestcheck WHERE signature='".$sign."' LIMIT 1;");
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
        
        $psy = '';
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
         {
//          $psy .= "<p><span class='badge'>".$val."</span> ".$key."</p>";
          $psy .= "<p><strong>".$key."</strong></p>";
          break;
         }
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
        
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
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswercheck WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
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
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
         {
//          $psy .= "<p><span class='badge'>".$val."</span> ".$key."</p>";
          $psy .= "<p><strong>".$key."</strong></p>";
          break;
         }
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
        break;
       }

        
       }
       else
       if ($aqc==$cnttd['count(*)'])
       {
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
          $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswercheck WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
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
        
         echo "<td>";
         foreach($psitest->getGroups() as $group)
          $keys[$group->getContent()] = $group->getBall();
         arsort($keys); 
         foreach($keys as $key => $val)
         {
//          $psy .= "<p><span class='badge'>".$val."</span> ".$key."</p>";
          $psy .= "<p><strong>".$key."</strong></p>";
          break;
         }
         echo $psy; 
         $psy = mysqli_real_escape_string($mysqli,$psy);
         echo "</td></tr>";
        
        $itog_sumball += $sumball;
        $itog_rightsumma += $rightsumma;
        $itog_nonsumma += $nonsumma;
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
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswercheck WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' AND value=1 ORDER BY id;");
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
?>
   </div>
  </div>
 </div>
 <div id="buttonset"> 
    <button class="ui-button-primary" style="font-size: 1em;" id="close" onclick="parent.closeFancybox();"><i class='fa fa-check fa-lg'></i> Закрыть</button> 
 </div>
</body>
</html>
