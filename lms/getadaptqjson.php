<?

  require_once "showquestion.php";
  include "config.php";
  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });

  if ($sqlanaliz)
  {
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $tstart = $mtime; 
  }

  $questid = intval($_POST['questid']);  
  $grid = intval($_POST['group']);  
  $num = intval($_POST['numid']);  
  $allq = intval($_POST['allq']);  
  $signature = $_POST['signature'];  
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

  $test = new Test($mysqli, null, $signature, USER_ID);
  if (!empty($test))
  {   

  $prefix = '';

  // Для проверочных тестов - изменим префикс БД
  if (md5($signature."check")==$mode or $test->getType()==='check')
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
  //WriteAnswer($mysqli, $ansqid, $prefix, $token, $multi, $strseq, $stracc1, $stracc2, $strkbd);

  if (!empty($ansqid))
  {
   $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$ansqid."' ORDER BY id LIMIT 1;");
   $quest = mysqli_fetch_array($qq);
   if (!empty($quest))
   {
          $groupid = $quest['qgroupid'];
          if ($quest['qtype']=='accord') // Соответствия
          {
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           mysqli_query($mysqli,"START TRANSACTION;");
           $qid = $quest['id'];
           
             if ($allcount==0)
             {
              for($k = 0, $size = count($stracc1); $k < $size; ++$k) {
               $value = substr($stracc1[$k],7);
               $value = _decode($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpaccord1".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               if ($sqlanaliz) $json['log2']=" insert accord 1 answer error";
              }
             }
             else
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($stracc1[$k],7);
               $value = _decode($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpaccord1".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                if ($sqlanaliz) $json['log2']=" update accord 1 answer error";
              }
              mysqli_free_result($ans);
             }
            
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           
             if ($allcount==0)
             {
              for($k = 0, $size = count($stracc2); $k < $size; ++$k) {
               $value = substr($stracc2[$k],7);
               $value = _decode2($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpaccord2".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               if ($sqlanaliz) $json['log2']=" insert accord 2 answer error";
              }
             }
             else                              
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($stracc2[$k],7);
               $value = _decode2($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpaccord2".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                if ($sqlanaliz) $json['log2']=" update accord 2 answer error";
              }
              mysqli_free_result($ans);
             }
            

            mysqli_query($mysqli,"COMMIT;");
          }
          else
          if ($quest['qtype']=='sequence') // Последовательность
          {
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           mysqli_query($mysqli,"START TRANSACTION;");
           $qid = $quest['id'];
             if ($allcount==0)
             {
              for($k = 0, $size = count($strseq); $k < $size; ++$k) {
               $value = substr($strseq[$k],6);
               $value = _decode($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpsequence".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               if ($sqlanaliz) $json['log2']=" insert sequence answer error";
              }
             }
             else
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($strseq[$k],6);
               $value = _decode($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpsequence".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                if ($sqlanaliz) $json['log2']=" update sequence answer error";
              }
              mysqli_free_result($ans);
             }
            
            mysqli_query($mysqli,"COMMIT;");
          }
          else
          if ($quest['qtype']=='multichoice')
          {
           $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM answers WHERE questionid='".$quest['id']."' ORDER BY id");
           $k=0;
           mysqli_query($mysqli,"START TRANSACTION;");
           while($answer = mysqli_fetch_array($ans))
           {
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpmultianswer".$prefix." WHERE questionid='".$quest['id']."' AND answerid='".$answer['id']."' AND signature='".$token."' LIMIT 1;");
             $total = mysqli_fetch_array($tmpanswer);
             $allcount = $total['count(*)'];
             $qid = $quest['id'];
             $aid = $answer['id'];
             $value = $multi[$k];
             $k++;
             if ($allcount==0)
             {
              if (!mysqli_query($mysqli,"INSERT INTO tmpmultianswer".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $aid,
                                        $value);"))
               if ($sqlanaliz) $json['log2']=" insert multi answer error";
             }
             else
              if (!mysqli_query($mysqli,"UPDATE tmpmultianswer".$prefix." SET value=".$value." WHERE questionid='".$qid."' AND answerid='".$aid."' AND signature='".$token."'"))
               if ($sqlanaliz) $json['log2']=" update multi answer error";
             mysqli_free_result($tmpanswer);
           }
           mysqli_query($mysqli,"COMMIT");
           mysqli_free_result($ans);
          }
          else
          if ($quest['qtype']=='shortanswer')
          {
             mysqli_query($mysqli,"START TRANSACTION;");
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpshortanswer".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
             $total = mysqli_fetch_array($tmpanswer);
             $allcount = $total['count(*)'];
             $qid = $quest['id'];
             if ($allcount==0)
             {
              if (!mysqli_query($mysqli,"INSERT INTO tmpshortanswer".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        '$strkbd');"))
               if ($sqlanaliz) $json['log2']=" update short answer error";
             }
             else
              if (!mysqli_query($mysqli,"UPDATE tmpshortanswer".$prefix." SET value='".$strkbd."' WHERE questionid='".$qid."' AND signature='".$token."'"))
               if ($sqlanaliz) $json['log2']=" update short answer error";
             mysqli_free_result($tmpanswer);
             mysqli_query($mysqli,"COMMIT");
          }
   }   
   mysqli_free_result($qq);
  }
  
   // Определим следующий вопрос или завершаем тест
   // 1. Заполним массив ответов
   $tmpquestions = array();
   $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmptest".$prefix." WHERE signature='".$token."' ORDER BY id;");
   while($testdata = mysqli_fetch_array($td))
    $tmpquestions[] = $testdata['questionid'];
   mysqli_free_result($td);

   if ($sqlanaliz) $json['log3']=" (".count($tmpquestions).") ";
   foreach($test->getGroups() as $group) 
   {
     if ($sqlanaliz) $json['log3'].=" | Группа ".$group->getName().": ";
     foreach($tmpquestions as $tmpq) 
      if (!empty($group->getQuestion($tmpq)))
       {
         $right = IsRightAnswer($mysqli, $tmpq, $token, $prefix);
         $exist = true;
         $group->addAnswer(new Answer($tmpq, 
                           $group->getId(), $right, $exist)); 
         if ($sqlanaliz) $json['log3'].="-".(int) $right;
       }
   }

   // Запишем последний ответ
   if (!empty($ansqid))
   {
//    $right = IsRightAnswer($mysqli, $ansqid, $token);
//    $exist = true;
    $group = $test->getGroup($groupid);
//    $group->addAnswer(new Answer($ansqid, $groupid, $right, $exist)); 
   }
   else
   {
//    $right = false;
    $group = $test->getGroup($grid);
   }
   
   $question = null;

   
   // Если последние два ответа правильные - попытка перехода на следующий уровень
   if ($group->getTwoLastRightAnswers())
   {
     if ($sqlanaliz) $json['log4']=" Up group";
     // два раза дан правильный ответ на этом уровне?
     // да - найдем группу следующего уровня сложности
     $group = $test->GetHighLevelGroup($group);
     // найдем еще не заданный вопрос
     if ($group->getAnswerCount() < $group->getCount() and $group->getAnswerCount() <= 6) // не более 7 вопросов на уровне
      $question = $group->getFindQuestion();
     // Иначе Все вопросы на этом уровне сложности заданы - завершаем тест
   }
   else
   // Если последние два ответа неправильные - попытка перехода на нижний уровень
   if ($group->getTwoLastNonRightAnswers())
   {
     if ($sqlanaliz) $json['log4']=" Down group";
     // два раза дан неправильный ответ на этом уровне?
     // да - найдем группу нижнего уровня сложности
     $group = $test->GetLowLevelGroup($group);
     // найдем еще не заданный вопрос
     if ($group->getAnswerCount() < $group->getCount() and $group->getAnswerCount() <= 6) // не более 7 вопросов на уровне
      $question = $group->getFindQuestion();
     // Иначе Все вопросы на этом уровне сложности заданы - завершаем тест
   }
   else
   {
     if ($sqlanaliz) $json['log4']=" Stay group";
     // нет - найдем еще не заданный вопрос на текущем уровне
     if ($group->getAnswerCount() < $group->getCount() and $group->getAnswerCount() <= 6) // не более 6 вопросов на уровне
       $question = $group->getFindQuestion();
     // Иначе Все вопросы на этом уровне сложности заданы - завершаем тест
   }
    
   // Пишем найденный вопрос
   if ($question != null)
   {
    $json['nextq'] = $question->getId();  
    $json['minutes'] = $question->getTime();
    $json['group'] = $group->getId();  
    
    if(defined("USER_ID")) 
     $userid = USER_ID;
    else
     $userid=0;
      
    if ($writeonly==0)
    {
     mysqli_query($mysqli,"START TRANSACTION;");
     $query = "INSERT INTO tmptest".$prefix." VALUES (0,
                                        ".$userid.",
                                        ".$question->getId().",
                                        ".$group->getId().",
                                        '".$token."',
                                        ".$test->getId().")";
     mysqli_query($mysqli,$query);
     mysqli_query($mysqli,"COMMIT;");
    }
   }
   else
   {
    $json['ok'] = 0;  
    $json['nextq'] = 0;  
    $json['minutes'] = 0;  
    $json['group'] = 0;  
    echo json_encode($json);
    exit();
   }
  
  $questid = $question->getId();
  
  if ($question != null and $writeonly==0) {
   // Покажем вопрос
   $s.= showquestion($mysqli, $questid, $prefix, $token, $num);
  }

  if ($sqlanaliz) $json['log4'].=" Next=".$json['nextq'];

  $s.="<input type='hidden' id='num' value='".$num."'>";
  
  $json['num'] = $num;  
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
  
  }
  else
   $json['ok'] = '0'; 
  
  echo json_encode($json); 
?>  