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
  
  if ($writeonly==0) {
        // Покажем текущий вопрос
        $s.= showquestion($mysqli, $questid, $prefix, $token, $num);         
        // Определим следующий
        $z=0;
        $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT questionid FROM tmptest".$prefix." WHERE signature='".$token."' ORDER BY id ASC;");
        while ($quest = mysqli_fetch_array($qq))
         {
          if ($z>0) 
          {
           $json['nextq'] = $quest['questionid'];  
           break;
          }
          if ($quest['questionid']==$questid) $z++;
         }
        mysqli_free_result($quest);
        $z=0;
        $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT questionid FROM tmptest".$prefix." WHERE signature='".$token."' ORDER BY id DESC;");
        while ($quest = mysqli_fetch_array($qq))
         {
          if ($z>0) 
          {
           $json['prevq'] = $quest['questionid'];  
           break;
          }
          if ($quest['questionid']==$questid) $z++;
         }
        mysqli_free_result($quest);  
  }

  if ($sqlanaliz) $json['log3']="prev=".$json['prevq']." next=".$json['nextq'];
  $s.="<input type='hidden' id='num' value='".$num."'>";
  
  $json['num'] = $num;  
  $json['allq'] = $allq;  
  $json['direction'] = $direction;  
  
  $json['content'] = htmlspecialchars_decode($s);  

  if ($sqlanaliz)
  {
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $tpassed = ($tend - $tstart); 
        $json['sqltime'] = $tpassed;
  }
  
  if(!empty($json['content']))  { 
             $json['ok'] = '1';  
  } else {  
             $json['ok'] = '0'; 
  }      
  echo json_encode($json); 
?>  