<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
{  

 include "config.php";
 include "func.php";

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

 $resultid = $_GET['id']; // ID Результата
 $rtf = $_GET['rtf']; // ID Результата
 if (empty($rtf)) $rtf = 0;

 $log=0;

 $query = "SELECT * FROM singleresult WHERE id='".$resultid."' LIMIT 1;";
 $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($res) 
  $result = mysqli_fetch_array($res);
 else 
  puterror("Ошибка при обращении к базе данных");
 $tid = $result['testid'];
 $userid = $result['userid'];
 $sign = $result['signature'];
 $itog_rightsumma = $result['rightball'];
 $itog_nonsumma = $result['allball'];
 $itog_sumball = $result['rightq'];
 $itog_allq = $result['allq'];
 $testdate = data_convert ($result['resdate'], 1, 0, 0);
 mysqli_free_result($res); 
 
 $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$userid."' LIMIT 1;");
 $fromuser = mysqli_fetch_array($from);
 $userfio = $fromuser['userfio'];
 mysqli_free_result($from);

 $query = "SELECT * FROM testgroups WHERE id=".$tid." LIMIT 1;";
 $tst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $query);
 if ($tst) 
  $test = mysqli_fetch_array($tst);
 else 
  puterror("Ошибка при обращении к базе данных");
 $testname = $test['name'];
 mysqli_free_result($tst); 

if ($rtf==0) 
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
    <link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
    <style type="text/css">
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 0px; bottom : 50px;  position: absolute; overflow: auto;} 
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
p {
  font: 16px / 1.4 'Helvetica', 'Arial', sans-serif;
}
</style>
<script>
  $(function() {
    $("#close").button();
    $("#print").button();
    $("a").button();
  });
  
  function getans(id) {
   $.post('getanswer.json',{qid:id},  
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#lnk'+id).empty();        
       $('#ans'+id).empty();        
       $('#ans'+id).append(obj.content);        
      } 
      else 
       alert("Ошибка при получении данных.");
    }); 
  } 
</script>
</head><body>
<div id="buttonsetm">

     <div class="panel panel-primary">
                        <div class="panel-heading">
                            Протокол тестирования по тесту <strong><?=$testname?></strong>&nbsp;<span class='pull-right'><?=$userfio?></span>
                        </div>
                        <div class="panel-body">

<?
}
else
{
$dir = dirname(__FILE__);
require_once $dir . '/lib/PHPRtfLite.php';

// register PHPRtfLite class loader
PHPRtfLite::registerAutoloader();

$rtfs = new PHPRtfLite();

$sect = $rtfs->addSection();

$sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
//$sect->addImage($dir . '/img/logoexpert.png', null );
$sect->writeText('<i>Протокол тестирования по тесту <b>'.$testname.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText('<i>Имя: <b>'.$userfio.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText('<i>Дата тестирования: <b>'.$testdate.'</b></i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
$sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));

}

  $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."' ORDER BY id;");
  $i=0;
  while($testdata = mysqli_fetch_array($td))
  {
       $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$testdata['questionid']."' ORDER BY id LIMIT 1;");
       $quest = mysqli_fetch_array($qq);
       $questname = $quest['content'];
       $questtype = $quest['qtype'];
       mysqli_free_result($qq); 

       $rtball=1; // По умолчанию - 1
       $qgp = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['qgroupid']."' LIMIT 1;");
       if ($qgp)
       {
         $questgp = mysqli_fetch_array($qgp);
         $rtball = $questgp['singleball'];
       } 
       else 
        if ($log>0 and $rtf==0) echo "err ";
       mysqli_free_result($qgp);
       $strans="";
              
       // Поищем правильные ответы
       if ($questtype=='accord')  // Просканируем соответствия
       {
         // Получим правильный ответ
         $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM answers WHERE questionid='".$testdata['questionid']."' ORDER BY id;");
         $rightanswers = array(); 
         while($answer = mysqli_fetch_array($ans))
          $rightanswers[] = $answer['id'];
         mysqli_free_result($ans);   

         $ball = 0;
         $kball = count($rightanswers);
         // Сравним с эталоном 
         $accord1 = array(); 
         $answer1 = array(); 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord1 WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' ORDER BY id;");
         while($useranswer = mysqli_fetch_array($userans))
         {
          $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM answers WHERE id='".$useranswer['answerid']."' LIMIT 1;");
          $answer = mysqli_fetch_array($ans);
          $pieces = explode("=", $answer['name']);
          $name = $pieces[0];
          $answer1[] = $name;
          mysqli_free_result($ans);
          $accord1[] = $useranswer['answerid'];
         }
         mysqli_free_result($userans);   

         $accord2 = array(); 
         $answer2 = array(); 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord2 WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' ORDER BY id;");
         while($useranswer = mysqli_fetch_array($userans))
         {
          $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM answers WHERE id='".$useranswer['answerid']."' LIMIT 1;");
          $answer = mysqli_fetch_array($ans);
          $pieces = explode("=", $answer['name']);
          $name = $pieces[1];
          $answer2[] = $name;
          mysqli_free_result($ans);
          $accord2[] = $useranswer['answerid'];
         }
         mysqli_free_result($userans);   
         
         for($ii = 0; $ii < $kball; ++$ii) {
          if (!empty($answer1[$ii]) and !empty($answer2[$ii]))
          { 
           if ($rtf==0)
            $strans .= "<p><strong>".$answer1[$ii]." <i class='fa fa-arrows-h fa-lg'></i> ".$answer2[$ii]."</strong></p>";
           else
            $strans .= "<b>".$answer1[$ii]." = ".$answer2[$ii]."</b>";
          }
         }
         for($i = 0; $i < $kball; $i++) {
           for($ii = 0; $ii < $kball; $ii++) {
             if ($accord1[$ii] == $rightanswers[$i] and $accord2[$ii] == $rightanswers[$i])
              $ball++;
          }
         }  
       }
       else
       if ($questtype=='sequence')
       {
         // Получим правильный ответ
         $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$testdata['questionid']."' ORDER BY id;");
         $rightanswers = array(); 
         while($answer = mysqli_fetch_array($ans))
          $rightanswers[] = $answer['id'];
         mysqli_free_result($ans);   
         $ball = 0;
         $kball = count($rightanswers);
         // Сравним с эталоном 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpsequence WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' ORDER BY id;");
         if ($userans)
         {
          $ii=0;
          while($useranswer = mysqli_fetch_array($userans))
          {
           $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM answers WHERE id='".$useranswer['answerid']."' LIMIT 1;");
           $answer = mysqli_fetch_array($ans);
           $name = $answer['name'];
           mysqli_free_result($ans);

           if ($rtf==0)
             $strans .= "<p><strong>".$name."</strong></p>";
           else
             $strans .= "<b>".$name."</b>";

           if ($useranswer['answerid'] == $rightanswers[$ii])
            $ball++;
           $ii++; 
          }
         }
         mysqli_free_result($userans);   
       }
       else
       {
     
       $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$testdata['questionid']."' ORDER BY id");
       $ball=0;
       $kball=0;
       $ansshow=0;
       while($answer = mysqli_fetch_array($ans))
       {
        if ($log>0 and $rtf==0) echo "<br>";
        
        if ($questtype=='multichoice')
        {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND answerid='".$answer['id']."' AND signature='".$sign."' LIMIT 1;");
         if ($userans)
         {
          if ($answer['ball']>0) 
           $kball++;
          $useranswer = mysqli_fetch_array($userans);
          $ku = $useranswer['value'];
          if (empty($ku))
           $ku = 0;
          if ($ku>0)
          {
           if ($rtf==0)
            $strans .= "<p><strong>".$answer['name']."</strong></p>";
           else
            $strans .= '<b>'.$answer['name'].'</b>';
          }
          if ($log>0 and $rtf==0) 
           echo "u=".$ku." a=".$answer['ball'];
          if ($ku and $answer['ball']>0) 
           $ball++;
          if ($ku and $answer['ball']==0) 
           $ball--;
         }
         mysqli_free_result($userans);   
        }
        else
        if ($questtype=='shortanswer')
        {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpshortanswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' ORDER BY id LIMIT 1;");
         if ($userans)
         {
          if ($answer['ball']>0) 
           $kball++;
          $useranswer = mysqli_fetch_array($userans);
          $kustr = trim(mb_strtolower($useranswer['value'],'UTF-8'));
          $ansk = trim(mb_strtolower($answer['name'],'UTF-8'));
          if ($ansshow==0) {
           if ($rtf==0)
            $strans .= "<p><strong>".$useranswer['value']."</strong></p>";
           else
            $strans .= 'Ответ: <b>'.$useranswer['value'].'</b>';
           $ansshow++;
          }
          if ($log>0 and $rtf==0) 
            echo "u=".$kustr." a=".$ansk." sign=".$sign." id=".$testdata['questionid'];
          if ($kustr == $ansk) 
           $ball++;
         }
         mysqli_free_result($userans);   
        }
       }
       mysqli_free_result($ans); 
       }
       
       if (empty($strans))
       {
        if ($rtf==0)
          $lnk = "<p>Ответа нет</p>";
         else
          $lnk = "<b>Ответа нет</b>";
        if ($rtf==0)
        {
         $lnk .= "<div id='lnk".$testdata['questionid']."'><a href='javascript:;' onclick=\"getans(".$testdata['questionid'].");\" title='Посмотреть правильный ответ на вопрос'><i class='fa fa-sort-alpha-asc'></i> Правильный ответ</a></div>";
         $lnk .= "<div id='ans".$testdata['questionid']."'></div>";
        }
       }
       else
       {
        $lnk = $strans;
        if ($ball!=$kball or $ball==0)
        {
         if ($rtf==0)
         {
          $lnk .= "<div id='lnk".$testdata['questionid']."'><a href='javascript:;' onclick=\"getans(".$testdata['questionid'].");\" title='Посмотреть правильный ответ на вопрос'><i class='fa fa-sort-alpha-asc'></i> Правильный ответ </a></div>";
          $lnk .= "<div id='ans".$testdata['questionid']."'></div>";
         }
        }
       }
       
       if ($questtype=='multichoice' or $questtype=='sequence' or $questtype=='accord')
       {
        if ($ball==$kball and $ball>0) 
        { 
         // Ну вот и правильный ответ
         if ($rtf==0)
         {
          echo "<p align='center'><strong>Вопрос №".++$i."</strong></p>
          <div class='alert alert-success alert-dismissable'><p>".$questname."</p>";
          echo $lnk;
          echo "<p>Баллов получено:".$rtball."</p>";
         }
         else
         {
          $sect->writeText('<i><b>Вопрос №'.++$i.'</b>: '.$questname.'</i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText($lnk, new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText('Баллов получено: <b>'.$rtball.'</b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
         }
        }
        else
        {
         if ($rtf==0)
         {
          echo "<p align='center'><strong>Вопрос №".++$i."</strong></p>
          <div class='alert alert-danger alert-dismissable'><p>".$questname."</p>";
          echo $lnk;
         }
         else
         {
          $sect->writeText('<b>Вопрос №'.++$i.'</b>: '.$questname.'', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText($lnk, new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText('Баллов получено: <b>0</b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
         }
        }
       }
       else
       if ($questtype=='shortanswer')
       {
        if ($ball>0) 
        { 
         // Ну вот и правильный ответ
         if ($rtf==0)
         {
          echo "<p align='center'><strong>Вопрос №".++$i."</strong></p><div id='menu_glide' style='padding:10px; background: #B2F5B6;' class='ui-widget-content ui-corner-all'><p>".$questname."</p>";
          echo $lnk;
          echo "<p>Баллов получено:".$rtball."</p>";
         }
         else
         {
          $sect->writeText('<i><b>Вопрос №'.++$i.'</b>: '.$questname.'</i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText($lnk, new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText('Баллов получено: <b>'.$rtball.'</b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
         }
        }
        else
        {
         if ($rtf==0)
         {
          echo "<p align='center'><strong>Вопрос №".++$i."</strong></p><div id='menu_glide' style='padding:10px; background: #FCC0C0;' class='ui-widget-content ui-corner-all'><tr style='background-color:#FCC0C0;'><p>".$questname."</p>";
          echo $lnk;
         }
         else
         {
          $sect->writeText('<i><b>Вопрос №'.++$i.'</b>: '.$questname.'</i>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText($lnk, new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText('Баллов получено: <b>0</b>', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
          $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT));
         }
        }
       }
      if ($rtf==0)
       echo "</div>";
  }
  mysqli_free_result($td); 
if ($rtf==0)
{
?>    
    </div>
   </div> 
  </div>
  <div id="buttonset"> 
     <a class="ui-button-primary" style="font-size: 1em;" href="resultprotocol&rtf=1&id=<? echo $resultid; ?>" id="print"><i class='fa fa-print fa-lg'></i> Печать</a>&nbsp; 
     <button class="ui-button-primary" style="font-size: 1em;" id="close" onclick="parent.closeFancybox();"><i class='fa fa-check fa-lg'></i> Закрыть</button> 
  </div>
</body></html>
<? 
}
else
{
header('Content-Type: application/octet-stream');
$filename = str2url("protocol_".$testname."_".$userfio).".rtf";
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

} else die;
?>