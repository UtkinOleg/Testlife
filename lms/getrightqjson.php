<?

  require_once "showquestion.php";
  include "config.php";

  if ($sqlanaliz)
  {
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $tstart = $mtime; 
  }

  $questid = intval($_POST['questid']);  
  $num = intval($_POST['numid']);  
  $allq = intval($_POST['allq']);  
  $token = $_POST['token'];  
  $direction = intval($_POST['direction']);  
  $writeonly = intval($_POST['writeonly']);  
  $strmulti = $_POST['strmulti'];  
  $strkbd = $_POST['kbd'];  
  $ansqid = intval($_POST['ansqid']);  
  $strseq = $_POST['seq'];  
  $stracc1 = $_POST['acc1'];  
  $stracc2 = $_POST['acc2'];  
  $mode = $_POST['m'];  
  $signature = $_POST['signature'];  

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE signature = '".$signature."' LIMIT 1");
  $test = mysqli_fetch_array($sql);
  $testtype = $test['testtype'];
  mysqli_free_result($sql);

  $prefix = '';

  // Для проверочных тестов - изменим префикс БД
  if (md5($signature."check")==$mode or $testtype=='check')
   $prefix = 'check';

  if ($prefix=='')
   if(!defined("USER_REGISTERED")) die;  
   
  $s="";
  if ($sqlanaliz) $json['log1']="qid=".$questid." token=".$token." multi=".$strmulti." ansqid=".$ansqid." strkbd=".$strkbd;
  if ($sqlanaliz) $json['log1'].=" acc1=".$stracc1." acc2=".$stracc2;
  
  if (!empty($strmulti)) $multi = explode("-", $strmulti);
  if (!empty($strseq)) $strseq = explode("&", $strseq);
  if (!empty($stracc1)) $stracc1 = explode("&", $stracc1);
  if (!empty($stracc2)) $stracc2 = explode("&", $stracc2);

  // запишем ответ в БД
  WriteAnswer($mysqli, $ansqid, $prefix, $token, $multi, $strseq, $stracc1, $stracc2, $strkbd);
  
  if ($prefix=='check') // Проверочный тест - покажем правильный ответ
  {
   $json['right'] = IsRightAnswer($mysqli, $ansqid, $token, $prefix);
   if (!$json['right'])
   {
    $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$ansqid."' ORDER BY id;");
    $sa="";
    while($answer = mysqli_fetch_array($ans))
    {
     if ($answer['ball']>0)
      $sa .= "<p><font size='+1'><i class='fa fa-check fa-lg'></i> ".$answer['name']."</font></p>";
    }
    mysqli_free_result($ans);
    
    // Покажем пояснение если есть
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT content2 FROM questions WHERE id='".$ansqid."' LIMIT 1;");
    $content = mysqli_fetch_array($sql);
    if (!empty($content))
      $sa .= "<p><font size='+1'>".$content['content2']."</font></p>";
    mysqli_free_result($sql);
    
    $json['rightdata'] = $sa;  
   }
  }

  if ($sqlanaliz)
  {
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $tpassed = ($tend - $tstart); 
        $json['sqltime'] = $tpassed;
  }
  
  $json['ok'] = '1';  
  echo json_encode($json); 
?>  